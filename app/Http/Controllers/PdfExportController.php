<?php

namespace App\Http\Controllers;

use mPDF;
use App\Models\Report;
use Illuminate\Http\Request;

class PdfExportController extends Controller
{
    public function export(Request $request)
    {
        // Ambil semua data report dari database
        $reports = Report::all();

        // HTML untuk tampilan laporan
        $html = view('pdf.reports', compact('reports'))->render();

        // Menggunakan mPDF untuk mengonversi HTML ke PDF
        $pdf = new \Mpdf\Mpdf();
        $pdf->WriteHTML($html);

        // Mengunduh file PDF
        return $pdf->Output('laporan_semua_report.pdf', 'D');
    }
}
