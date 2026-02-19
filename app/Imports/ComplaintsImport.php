<?php

namespace App\Imports;

use App\Models\Complaint;
use App\Models\Source;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class ComplaintsImport implements ToModel, WithHeadingRow
{
    /**
     * BARIS HEADER
     */
    public function headingRow(): int
    {
        return 2; // Mulai baca header dari baris ke-2
    }

    /**
    * Menerjemahkan Baris Excel ke Database
    */
    public function model(array $row)
    {
        // MAPPING KOLOM
        $tglMasuk    = $row['tanggal_masuk'] ?? $row['tgl_masuk'] ?? $row['tanggal'] ?? null;
        $unitPelapor = $row['unit_pelapor'] ?? $row['pelapor'] ?? 'Masyarakat Umum';
        $namaPelapor = $row['nama_pelapor'] ?? $row['nama'] ?? '-';
        $namaMedia   = $row['media'] ?? $row['sumber'] ?? null;
        $isiKeluhan  = $row['isi_keluhan'] ?? $row['keluhan'] ?? $row['deskripsi'] ?? '-';
        $unitTujuan  = $row['unit_tujuan'] ?? $row['tujuan'] ?? 'Humas';
        $grade       = $row['grade'] ?? $row['kegawatan'] ?? 'Hijau';

        // Filter: Jika tanggal atau isi keluhan kosong, jangan diimport
        if (!$tglMasuk || !$isiKeluhan) {
            return null;
        }

        // FORMAT TANGGAL
        try {
            if (is_numeric($tglMasuk)) {
                $date = Date::excelToDateTimeObject($tglMasuk);
            } else {
                $date = Carbon::parse($tglMasuk);
            }
        } catch (\Exception $e) {
            $date = Carbon::now(); 
        }

        // CARI ID MEDIA
        $source = Source::where('name', 'like', '%' . $namaMedia . '%')->first();
        $sourceId = $source ? $source->id : 1; 

        // SIMPAN
        return new Complaint([
            'user_id'           => Auth::id(), 
            'date'              => $date,
            'reporter_type'     => $unitPelapor,
            'reporter_name'     => $namaPelapor,
            'source_id'         => $sourceId,
            'description'       => $isiKeluhan,
            'unit_destination'  => $unitTujuan,
            'grade'             => $grade,
            'status'            => 'Pending', 
        ]);
    }
}