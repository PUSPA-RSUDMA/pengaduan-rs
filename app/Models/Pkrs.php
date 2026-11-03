<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pkrs extends Model
{
    use HasFactory;

    // Menentukan nama tabel secara spesifik (opsional jika nama tabel sama dengan nama model + s)
    protected $table = 'pkrs';

    // Menentukan kolom mana saja yang boleh diisi (Mass Assignment)
    protected $fillable = [
        'tanggal',
        'hari',
        'tema',
        'rencana_kegiatan',
        'request_by',
        'output',
        'bukti',
        'status',
    ];
}