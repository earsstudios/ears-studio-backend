<?php

namespace App\Http\Controllers;

use App\Models\Report;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\Response;

class ExcelExportController extends Controller
{
    public function export()
    {
        // Ambil data dari model
        $reports = Report::all(['program_name', 'beneficiaries', 'province', 'city', 'district', 'distribution_date', 'additional_notes', 'status', 'rejection_reason']);

        // Buat spreadsheet baru
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Tambahkan header kolom
        $headers = [
            'Nama Program',
            'Jumlah Penerima Manfaat',
            'Provinsi',
            'Kota',
            'Kecamatan',
            'Tanggal Distribusi',
            'Catatan Tambahan',
            'Status',
            'Alasan Penolakan',
        ];

        $sheet->fromArray($headers, null, 'A1'); // Isi header di baris pertama

        // Tambahkan data
        $row = 2; // Mulai dari baris kedua
        foreach ($reports as $report) {
            $sheet->fromArray([
                $report->program_name,
                $report->beneficiaries,
                $report->province,
                $report->city,
                $report->district,
                $report->distribution_date,
                $report->proof_file,
                $report->additional_notes,
                $report->status,
                $report->rejection_reason,
            ], null, 'A' . $row);

            $row++;
        }

        // Simpan spreadsheet ke memori
        $writer = new Xlsx($spreadsheet);
        $fileName = 'laporan_reports.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);
        $writer->save($temp_file);

        // Return response untuk download file
        return Response::download($temp_file, $fileName)->deleteFileAfterSend(true);
    }
}
