<?php

namespace App\Exports;

use App\Models\Pkrs;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class PkrsExport implements FromCollection, WithHeadings, WithMapping, WithColumnWidths, WithStyles
{
    private $rowNumber = 0;
    private $search;
    private $status;
    private $sort;

    public function __construct($search = null, $status = null, $sort = 'desc')
    {
        $this->search = $search;
        $this->status = $status;
        $this->sort   = $sort === 'asc' ? 'asc' : 'desc';
    }

    /**
     * 1. Ambil Data Kegiatan PKRS (Mengikuti Filter Aktif)
     */
    public function collection()
    {
        $query = Pkrs::query();

        if (!empty($this->search)) {
            $search = $this->search;
            $query->where(function ($q) use ($search) {
                $q->where('tema', 'like', "%{$search}%")
                  ->orWhere('rencana_kegiatan', 'like', "%{$search}%");
            });
        }

        if (!empty($this->status)) {
            $query->where('status', $this->status);
        }

        return $query->orderBy('tanggal', $this->sort)->get();
    }

    /**
     * 2. Judul & Header Tabel
     */
    public function headings(): array
    {
        return [
            ['LAPORAN DATA KEGIATAN PROMOSI KESEHATAN RUMAH SAKIT (PKRS)'],
            [
                'No',
                'Tanggal',
                'Hari',
                'Tema / Peringatan',
                'Rencana Kegiatan',
                'Request By',
                'Output',
                'Bukti / Link',
                'Status',
            ],
        ];
    }

    /**
     * 3. Petakan Data per Baris
     */
    public function map($item): array
    {
        $this->rowNumber++;

        return [
            $this->rowNumber,
            \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y'),
            $item->hari,
            $item->tema,
            $item->rencana_kegiatan,
            $item->request_by,
            $item->output,
            $item->bukti ?: '-',
            $item->status,
        ];
    }

    /**
     * 4. Atur Lebar Kolom
     */
    public function columnWidths(): array
    {
        return [
            'A' => 6,
            'B' => 15,
            'C' => 12,
            'D' => 30,
            'E' => 45,
            'F' => 22,
            'G' => 22,
            'H' => 35,
            'I' => 14,
        ];
    }

    /**
     * 5. Styling Tampilan Excel
     */
    public function styles(Worksheet $sheet)
    {
        // Merge baris 1 dari kolom A sampai I untuk Judul
        $sheet->mergeCells('A1:I1');

        // Wrap text untuk kolom Tema, Rencana Kegiatan, dan Bukti
        $sheet->getStyle('D:E')->getAlignment()->setWrapText(true);
        $sheet->getStyle('H')->getAlignment()->setWrapText(true);

        // Rata atas untuk semua
        $sheet->getStyle('A:I')->getAlignment()->setVertical(Alignment::VERTICAL_TOP);

        // Styling Header Tabel (Baris 2)
        $sheet->getStyle('A2:I2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A2:I2')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A2:I2')->getFont()->setBold(true);

        // Styling Judul Besar (Baris 1)
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

        // Tengah-tengahkan kolom No, Tanggal, Hari, Request By, Output, Status
        $highestRow = $sheet->getHighestRow();
        if ($highestRow >= 3) {
            $sheet->getStyle('A3:C' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('F3:G' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('I3:I' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        return [];
    }
}
