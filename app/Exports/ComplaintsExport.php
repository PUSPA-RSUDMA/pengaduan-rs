<?php

namespace App\Exports;

use App\Models\Complaint;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnWidths; // FITUR 1: Atur Lebar
use Maatwebsite\Excel\Concerns\WithStyles;       // FITUR 2: Atur Gaya
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;    // FITUR 3: Posisi Teks

class ComplaintsExport implements FromView, WithColumnWidths, WithStyles
{
    public function view(): View
    {
        return view('exports.excel', [
            'complaints' => Complaint::orderBy('date', 'desc')->get()
        ]);
    }

    /**
     * ATUR LEBAR KOLOM MANUAL
     */
    public function columnWidths(): array
    {
        return [
            'A' => 5,   // No
            'B' => 15,  // Tanggal Masuk
            'C' => 20,  // Unit Pelapor
            'D' => 15,  // Media
            'E' => 10,  // Grade
            'F' => 60,  // Isi Keluhan
            'G' => 20,  // Unit Tujuan
        ];
    }

    /**
     * ATUR GAYA TULISAN (STYLE)
     */
    public function styles(Worksheet $sheet)
    {
        // WRAP TEXT
        $sheet->getStyle('F')->getAlignment()->setWrapText(true);

        // RATA ATAS
        $sheet->getStyle('A:G')->getAlignment()->setVertical(Alignment::VERTICAL_TOP);
        
        // RATA TENGAH UNTUK HEADER & KOLOM TERTENTU
        // Baris 2 adalah Header Kolom (No, Tgl, dll)
        $sheet->getStyle('2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('2')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('2')->getFont()->setBold(true);

        // Kolom A, B, D, E (No, Tgl, Media, Grade)
        $sheet->getStyle('A')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('B')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('D')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('E')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // JUDUL BESAR (Baris 1)
        $sheet->getStyle('1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        return [];
    }
}