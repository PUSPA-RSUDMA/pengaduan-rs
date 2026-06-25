<table>
    <thead>
        {{-- Baris 1: Judul Laporan --}}
        <tr>
            <th colspan="8" align="center" style="font-weight: bold; font-size: 14px;">
                LAPORAN DATA LAYANAN PENGADUAN & INFORMASI
            </th>
        </tr>
        {{-- Baris 2: Header Kolom --}}
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
            <td align="center">{{ $loop->iteration }}</td>
            <td align="center">{{ \Carbon\Carbon::parse($item->tgl_masuk)->format('d/m/Y') }}</td>
            {{-- Tambahkan tanda petik satu agar nomor HP tidak hilang angka 0 di depannya saat masuk Excel --}}
            <td align="center">="{{ $item->no_hp }}"</td>
            <td align="center">{{ $item->metode_penyampaian }}</td>
            <td align="center">{{ $item->jenis_permintaan }}</td>
            <td>{{ $item->uraian }}</td>
            <td align="center">{{ $item->unit_terkait }}</td>
            <td align="center">
                {{ $item->tgl_verifikasi ? \Carbon\Carbon::parse($item->tgl_verifikasi)->format('d/m/Y') : '-' }}
            </td>
        </tr>
        @endforeach
    </tbody>
</table>