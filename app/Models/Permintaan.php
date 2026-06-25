<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permintaan extends Model
{
    use HasFactory;

    // Pastikan nama tabel sesuai (opsional jika nama tabel sudah 'permintaans')
    protected $table = 'permintaans'; 

    // TAMBAHKAN 'user_id' DI SINI
    protected $fillable = [
        'user_id',
        'tgl_masuk',
        'no_hp',
        'metode_penyampaian',
        'jenis_permintaan',
        'uraian',
        'unit_terkait',
        'tgl_verifikasi',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}