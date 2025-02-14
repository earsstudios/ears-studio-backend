<?php

namespace App\Exports;

use App\Models\Report;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class ReportExport implements FromCollection, WithHeadings, WithTitle
{
    public function collection()
    {
        return Report::all([
            'program_name',
            'beneficiaries',
            'province',
            'city',
            'district',
            'distribution_date',
            'proof_file',
            'additional_notes',
            'status',
            'rejection_reason',
        ]);
    }

    public function headings(): array
    {
        return [
            'Nama Program',
            'Jumlah Penerima Manfaat',
            'Provinsi',
            'Kota',
            'Kecamatan',
            'Tanggal Distribusi',
            'Berkas Bukti',
            'Catatan Tambahan',
            'Status',
            'Alasan Penolakan',
        ];
    }

    public function title(): string
    {
        return 'Laporan Semua Report';
    }
}
