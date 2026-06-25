<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Export Layanan Pengaduan & Informasi</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .table th, .table td { border: 1px solid #000; padding: 6px; }
        .table th { background-color: #f2f2f2; text-align: center; font-weight: bold; }
        .text-center { text-align: center; }
        .title { text-align: center; font-size: 18px; font-weight: bold; margin-bottom: 20px; text-transform: uppercase; }
    </style>
</head>
<body>

    <div class="title">
        LAPORAN DATA LAYANAN PENGADUAN & INFORMASI
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>No</th>
                <th>Tgl Masuk</th>
                <th>No HP</th>
                <th>Metode</th>
                <th>Jenis Permintaan</th>
                <th>Uraian</th>
                <th>Unit Terkait</th>
                <th>Tgl Verifikasi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($permintaans as $item)
            <tr>
                <td class="text-center">{{ $loop->iteration }}</td>
                <td class="text-center">{{ \Carbon\Carbon::parse($item->tgl_masuk)->format('d/m/Y') }}</td>
                <td class="text-center">{{ $item->no_hp }}</td>
                <td class="text-center">{{ $item->metode_penyampaian }}</td>
                <td class="text-center">{{ $item->jenis_permintaan }}</td>
                <td>{{ $item->uraian }}</td>
                <td>{{ $item->unit_terkait }}</td>
                <td class="text-center">
                    {{ $item->tgl_verifikasi ? \Carbon\Carbon::parse($item->tgl_verifikasi)->format('d/m/Y') : '-' }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>