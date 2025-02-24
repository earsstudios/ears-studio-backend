<?php

namespace App\Http\Controllers;

use App\Services\DdexValidatorService;
use Illuminate\Http\Request;
use DedexBundle\Controller\ErnParserController;
use DedexBundle\Simplifiers\SimpleAlbum;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class DdexValidatorController extends Controller
{
    protected $parserService;

    public function __construct(DdexValidatorService $parserService)
    {
        // $this->validatorService = $validatorService;
        $this->parserService = $parserService;
    }

    // public function validateData(Request $request)
    // {
    //     $validated = $request->validate([
    //         'xml' => 'required|string', // Validasi jika XML ada dalam body request
    //     ]);

    //     // Menjalankan validasi metadata
    //     $validationResult = $this->validatorService->validateMetadata($validated);

    //     return response()->json($validationResult);
    // }

    // public function validateMusic(Request $request)
    // {
    //     // Hardcode data yang akan dimasukkan ke XML
    //     $albumTitle = 'My Awesome Album';
    //     $artistName = 'The Great Artist';
    //     $releaseDate = '2023-01-01';
    //     $trackCount = 10;

    //     // Menyusun XML DDEX
    //     // Membuat objek SimpleXMLElement tanpa mendefinisikan prefix di root
    //     $xml = new \SimpleXMLElement('<PurgeReleaseMessage/>');
        
    //     // Menambahkan namespace ke root dan elemen lainnya
    //     $xml->addAttribute('xmlns:ernm', 'http://ddex.net/xml/ern/382');

    //     // Menambahkan elemen-elemen XML
    //     $messageHeader = $xml->addChild('MessageHeader');
    //     $messageHeader->addChild('MessageThreadId', '123456');
    //     $messageHeader->addChild('MessageId', '78910');

    //     // ... (tambahkan elemen lainnya sesuai kebutuhan)

    //     $purgedRelease = $xml->addChild('PurgedRelease');
    //     $releaseId = $purgedRelease->addChild('ReleaseId');
    //     $releaseId->addChild('ICPN', '1234567890123');

    //     $title = $purgedRelease->addChild('Title');
    //     $title->addAttribute('LanguageAndScriptCode', 'en');
    //     $title->addChild('TitleText', $albumTitle); // Menggunakan data hardcode

    //     // Menghasilkan XML sebagai string
    //     $xmlString = $xml->asXML();

    //     // Kirim XML sebagai respons dengan tipe konten 'application/xml'
    //     return response($xmlString, 200)
    //         ->header('Content-Type', 'application/xml');
    // }

    public function validateMusic(Request $request)
    {
        // Hardcode data yang akan dimasukkan ke XML
        $albumTitle = 'My Awesome Album';
        $artistName = 'The Great Artist';
        $releaseDate = '2023-01-01';
        $trackCount = 10;

        // Membuat DOMDocument baru
        $doc = new \DOMDocument('1.0', 'UTF-8');

        // Menambahkan deklarasi namespace
        $root = $doc->createElementNS('http://ddex.net/xml/ern/382', 'ernm:PurgeReleaseMessage');
        $doc->appendChild($root);

        // Menambahkan elemen-elemen ke root
        $messageHeader = $doc->createElement('MessageHeader');
        $messageHeader->appendChild($doc->createElement('MessageThreadId', '123456'));
        $messageHeader->appendChild($doc->createElement('MessageId', '78910'));
        $root->appendChild($messageHeader);

        // Menambahkan elemen PurgedRelease
        $purgedRelease = $doc->createElement('PurgedRelease');
        $releaseId = $doc->createElement('ReleaseId');
        $releaseId->appendChild($doc->createElement('ICPN', '1234567890123'));
        $purgedRelease->appendChild($releaseId);

        // Menambahkan title
        $title = $doc->createElement('Title');
        $title->setAttribute('LanguageAndScriptCode', 'en');
        $title->appendChild($doc->createElement('TitleText', $albumTitle));
        $purgedRelease->appendChild($title);

        $root->appendChild($purgedRelease);

        // Menghasilkan XML string
        $xmlString = $doc->saveXML();

        // Simpan XML ke dalam file sementara
        $filePath = storage_path('app/public/music.xml');
        file_put_contents($filePath, $xmlString);

        // Kirimkan file XML sebagai unduhan
        return response()->download($filePath, 'music.xml', [
            'Content-Type' => 'application/xml',
        ]);
    }

    public function validateData(Request $request)
    {
         // Validasi file XML yang diupload
         $request->validate([
            'xml_file' => 'required|file|mimes:xml|max:10240', // Max 10MB
        ]);

        try {
            // Ambil file XML yang diupload
            $xmlFile = $request->file('xml_file');
            
            // Pindahkan file ke direktori penyimpanan sementara
            $tempPath = storage_path('app/temp.xml');
            $xmlFile->move(storage_path('app'), 'temp.xml');
            
            // Parse XML menggunakan ErnParserController
            $parser = new ErnParserController();
            $ern = $parser->parse($tempPath); // Parsing dari file XML sementara

            // Gunakan SimpleAlbum untuk mendapatkan data dari XML
            $album = new SimpleAlbum($ern);

            // Mengakses properti level tinggi dari album
            $releaseDate = $album->getOriginalReleaseDate();
            $artistsAtAlbumLevel = $album->getArtists();
            $tracksForCd1 = $album->getTracksPerCd()[1];

            // Mengakses mapping XML yang lebih spesifik (DDex objects)
            $ddexRelease = $album->getDdexRelease();
            $icpnValue = $ddexRelease->getReleaseId()[0]->getICPN()->value(); // Mendapatkan ICPN
            $ddexTrack = $tracksForCd1[1]->getDdexSoundRecording()->getDuration(); // Mendapatkan Durasi track

            // Menyusun respons dalam bentuk array
            $response = [
                'success' => true,
                'data' => [
                    'release_date' => $releaseDate,
                    'artists_at_album_level' => $artistsAtAlbumLevel,
                    'tracks_for_cd_1' => $tracksForCd1,
                    'ddex_release_icpn' => $icpnValue,
                    'ddex_track_duration' => $ddexTrack,
                ],
            ];

            // Hapus file sementara setelah selesai diproses
            File::delete($tempPath);

            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function uploadXml(Request $request)
    {
        // Validasi file XML yang diupload
        $validated = $request->validate([
            'xml' => 'required|file|mimes:xml|max:10240', // Maksimum 10MB
        ]);

        // Ambil file XML yang diupload
        $xmlFile = $request->file('xml');

        // Pastikan file XML valid
        if (!$xmlFile || !$xmlFile->isValid()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid XML file.',
            ], 400);
        }

        // Membaca konten file XML
        $xmlContent = simplexml_load_file($xmlFile->getRealPath());

        if (!$xmlContent) {
            return response()->json([
                'success' => false,
                'message' => 'Error parsing XML.',
            ], 500);
        }

        // Konversi XML menjadi array
        $json = json_encode($xmlContent);
        $arrayData = json_decode($json, true);

        // Kembalikan data sebagai JSON
        return response()->json([
            'success' => true,
            'data' => $arrayData,
        ]);
    }
}
