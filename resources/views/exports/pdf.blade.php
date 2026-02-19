<!DOCTYPE html>
<html>
<head>
    <title>Laporan Pengaduan RSUD</title>
    <style>
        body { font-family: sans-serif; font-size: 10pt; margin: 0; padding: 0; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 0; padding: 0; text-transform: uppercase; font-size: 14pt; }
        .header h4 { margin: 5px 0; font-weight: normal; font-size: 11pt; }
        .line { border-bottom: 2px solid black; margin-top: 10px; margin-bottom: 20px; }
        
        /* TABLE STYLING */
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid black; padding: 8px; vertical-align: middle; }
        th { background-color: #e0e0e0; font-weight: bold; text-align: center; height: 30px; }
        .text-center { text-align: center; }
        
        @page { margin: 2cm 1.5cm; }
    </style>
</head>
<body>

    <div class="header">
        <h2>PEMERINTAH KABUPATEN SUMENEP</h2>
        <h2>RUMAH SAKIT UMUM DAERAH (RSUD)</h2>
        <h4>Jl. DR. Cipto No.42, Telp (0328) 662494</h4>
        <div class="line"></div>
        
        <h3 style="text-decoration: underline; margin-bottom: 5px; font-size: 12pt;">LAPORAN DATA PENGADUAN MASUK</h3>
        <h4 style="margin-top: 0; font-size: 11pt;">KELUHAN EKSTERNAL {{ date('Y') }}</h4>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="12%">Tgl Masuk</th>
                <th width="18%">Unit Pelapor</th>
                <th width="12%">Media</th>
                <th width="10%">Grade</th> {{-- Kolom Grade --}}
                <th>Isi Keluhan</th>
                <th width="15%">Unit Tujuan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($complaints as $complaint)
            <tr>
                <td class="text-center">{{ $loop->iteration }}</td>
                <td class="text-center">{{ $complaint->created_at->format('d/m/Y') }}</td>
                <td>{{ $complaint->reporter_type }}</td>
                <td class="text-center">{{ $complaint->source->name ?? '-' }}</td>

                {{-- LOGIKA WARNA FULL CELL --}}
                @php
                    $cssColor = '#ffffff'; // Default Putih
                    $val = $complaint->grade;
                    
                    if (Str::startsWith($val, '#')) {
                        $cssColor = $val;
                    } else {
                        if (stripos($val, 'Merah') !== false) $cssColor = '#dc3545';
                        elseif (stripos($val, 'Kuning') !== false) $cssColor = '#ffc107';
                        elseif (stripos($val, 'Hijau') !== false) $cssColor = '#198754';
                    }
                @endphp

                {{-- Disini kuncinya: Background ditaruh di TD, bukan div --}}
                <td style="background-color: {{ $cssColor }}; border: 1px solid black;">
                    {{-- Kosongkan isinya agar hanya warna --}}
                </td>

                <td style="text-align: justify;">{{ $complaint->description }}</td>
                <td>{{ $complaint->unit_destination ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>