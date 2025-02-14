<?php
namespace App\Services;

use DedexBundle\Controller\ErnParserController;
use DedexBundle\Simplifiers\SimpleAlbum;

class DdexValidatorService
{
    public function validateMetadata(array $metadata)
    {
        // Menyimpan metadata sebagai file sementara (misalnya XML)
        $xml_path = public_path('temp/metadata.xml');
        if (!is_dir(public_path('temp'))) {
            mkdir(public_path('temp'), 0775, true);  // Membuat folder jika tidak ada
        }
        file_put_contents($xml_path, $metadata['xml']); // Asumsi metadata berisi file XML

        $parser = new ErnParserController();
        $ern = $parser->parse($xml_path);

        // Menggunakan SimpleAlbum untuk mengakses informasi album
        $album = new SimpleAlbum($ern);
        $release_date = $album->getOriginalReleaseDate();

        return [
            'release_date' => $release_date,
            'artists' => $album->getArtists(),
        ];
    }

    public function parseXml($xmlContent)
    {
        // Simpan XML ke file sementara
        $xml_path = public_path('temp/metadata.xml');
        if (!is_dir(public_path('temp'))) {
            mkdir(public_path('temp'), 0775, true);  // Membuat folder jika tidak ada
        }
        file_put_contents($xml_path, $xmlContent['xml']); 

        // Parsing XML menggunakan DedexBundle
        $parser = new ErnParserController();
        $ern = $parser->parse($xml_path);

        // Ambil informasi album menggunakan SimpleAlbum
        $album = new SimpleAlbum($ern);

        // Ambil informasi spesifik dari album
        $releaseDate = $album->getOriginalReleaseDate();
        $artists = $album->getArtists();
        $tracks = $album->getTracksPerCd();

        return [
            'release_date' => $releaseDate,
            'artists' => $artists,
            'tracks' => $tracks,
        ];
    }

    
}


