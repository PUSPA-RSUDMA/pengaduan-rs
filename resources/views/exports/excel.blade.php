<table>
    <thead>
    {{-- JUDUL BESAR --}}
    <tr>
        <th colspan="7" style="font-weight: bold; font-size: 16px; text-align: center; height: 35px; vertical-align: middle;">
            KELUHAN EKSTERNAL {{ date('Y') }}
        </th>
    </tr>
    
    {{-- HEADER DENGAN UKURAN LEBAR KOLOM (WIDTH) SUPAYA RAPI --}}
    <tr style="background-color: #ffff00; border: 1px solid #000000;">
        <th style="width: 5px; font-weight: bold; text-align: center; border: 1px solid #000000;">No</th>
        <th style="width: 15px; font-weight: bold; text-align: center; border: 1px solid #000000;">Tanggal Masuk</th>
        <th style="width: 25px; font-weight: bold; text-align: center; border: 1px solid #000000;">Unit Pelapor</th>
        <th style="width: 15px; font-weight: bold; text-align: center; border: 1px solid #000000;">Media</th>
        <th style="width: 10px; font-weight: bold; text-align: center; border: 1px solid #000000;">Grade</th>
        <th style="width: 50px; font-weight: bold; text-align: center; border: 1px solid #000000;">Isi Keluhan</th>
        <th style="width: 25px; font-weight: bold; text-align: center; border: 1px solid #000000;">Unit Tujuan</th>
    </tr>
    </thead>
    <tbody>
    @foreach($complaints as $complaint)
        @php
            // Logika Warna untuk Excel
            $excelColor = '#ffffff'; 
            $val = $complaint->grade;
            
            if (Str::startsWith($val, '#')) {
                $excelColor = $val;
            } else {
                if (stripos($val, 'Merah') !== false) $excelColor = '#dc3545';
                elseif (stripos($val, 'Kuning') !== false) $excelColor = '#ffc107';
                elseif (stripos($val, 'Hijau') !== false) $excelColor = '#198754';
            }
        @endphp

        <tr>
            <td style="text-align: center; border: 1px solid #000000; vertical-align: top;">{{ $loop->iteration }}</td>
            <td style="text-align: center; border: 1px solid #000000; vertical-align: top;">{{ \Carbon\Carbon::parse($complaint->date)->format('d/m/Y') }}</td>
            <td style="border: 1px solid #000000; vertical-align: top;">{{ $complaint->reporter_type }}</td>
            <td style="text-align: center; border: 1px solid #000000; vertical-align: top;">{{ $complaint->source->name ?? '-' }}</td>
            
            {{-- WARNA GRADE FULL CELL --}}
            {{-- Teks warnanya disamakan dengan background supaya 'hilang' tapi sel tetap berwarna --}}
            <td style="background-color: {{ $excelColor }}; color: {{ $excelColor }}; border: 1px solid #000000;">
                {{ $val }}
            </td>
            
            <td style="border: 1px solid #000000; vertical-align: top; word-wrap: break-word;">{{ $complaint->description }}</td>
            <td style="border: 1px solid #000000; vertical-align: top;">{{ $complaint->unit_destination }}</td>
        </tr>
    @endforeach
    </tbody>
</table>