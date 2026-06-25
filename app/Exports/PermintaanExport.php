<?php

namespace App\Exports;

use App\Models\Permintaan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class PermintaanExport implements FromCollection, WithHeadings, WithMapping, WithColumnWidths, WithStyles
{
    private $rowNumber = 0; // Untuk auto-increment nomor urut

    /**
     * 1. Ambil Datanya
     */
    public function collection()
    {
        return Permintaan::orderBy('tgl_masuk', 'desc')->get();
    }

    /**
     * 2. Buat Judul & Header Tabel
     */
    public function headings(): array
    {
        return [
            ['LAPORAN DATA LAYANAN PENGADUAN & INFORMASI'], // Baris 1 (Akan di-merge)
            [
                'No',
                'Tgl Masuk',
                'No HP',
                'Metode',
                'Jenis Permintaan',
                'Uraian',
                'Unit Terkait',
                'Tgl Verifikasi'
            ] // Baris 2 (Header Tabel)
        ];
    }

    /**
     * 3. Petakan / Masukkan Data per Baris
     */
    public function map($item): array
    {
        $this->rowNumber++;

        return [
            $this->rowNumber,
            \Carbon\Carbon::parse($item->tgl_masuk)->format('d/m/Y'),
            // Trik ="" agar angka 0 di depan No HP tidak hilang di Excel
            '="' . $item->no_hp . '"', 
            $item->metode_penyampaian,
            $item->jenis_permintaan,
            $item->uraian,
            $item->unit_terkait,
            $item->tgl_verifikasi ? \Carbon\Carbon::parse($item->tgl_verifikasi)->format('d/m/Y') : '-',
        ];
    }

    /**
     * 4. Atur Lebar Kolom
     */
    public function columnWidths(): array
    {
        return [
            'A' => 5,
            'B' => 15,
            'C' => 18,
            'D' => 15,
            'E' => 15,
            'F' => 50,
            'G' => 20,
            'H' => 15,
        ];
    }

    /**
     * 5. Styling Tampilan Excel
     */
    public function styles(Worksheet $sheet)
    {
        // Merge baris 1 dari kolom A sampai H untuk Judul
        $sheet->mergeCells('A1:H1');

        // Wrap text untuk kolom Uraian
        $sheet->getStyle('F')->getAlignment()->setWrapText(true);

        // Rata atas untuk semua
        $sheet->getStyle('A:H')->getAlignment()->setVertical(Alignment::VERTICAL_TOP);

        // Styling Header Tabel (Baris 2)
        $sheet->getStyle('A2:H2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A2:H2')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A2:H2')->getFont()->setBold(true);

        // Styling Judul Besar (Baris 1)
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

        // Tengah-tengahkan kolom data (Mulai baris ke-3)
        $highestRow = $sheet->getHighestRow();
        if ($highestRow >= 3) {
            $sheet->getStyle('A3:E' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('H3:H' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        return [];
    }
}