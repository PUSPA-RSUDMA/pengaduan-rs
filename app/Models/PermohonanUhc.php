<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermohonanUhc extends Model
{
    use HasFactory;

    protected $table = 'permohonan_uhc';

    protected $fillable = [
        'nama_pemohon',
        'nama_pasien',
        'no_hp',
        'segmen_kepesertaan',
        'alasan_peralihan',
        'file_lampiran',
    ];
}