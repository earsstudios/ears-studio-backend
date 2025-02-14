<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Semua Report</title>
</head>
<body>
    <h1>Laporan Semua Report</h1>

    <table border="1" cellpadding="5" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Program</th>
                <th>Jumlah Penerima Manfaat</th>
                <th>Provinsi</th>
                <th>Kota</th>
                <th>Kecamatan</th>
                <th>Tanggal Distribusi</th>
                <th>Catatan Tambahan</th>
                <th>Status</th>
                <th>Alasan Penolakan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($reports as $index => $report)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $report->program_name }}</td>
                    <td>{{ $report->beneficiaries }}</td>
                    <td>{{ $report->province }}</td>
                    <td>{{ $report->city }}</td>
                    <td>{{ $report->district }}</td>
                    <td>{{ $report->distribution_date->format('d-m-Y') }}</td>
                    <td>{{ $report->additional_notes }}</td>
                    <td>{{ $report->status }}</td>
                    <td>{{ $report->rejection_reason }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
