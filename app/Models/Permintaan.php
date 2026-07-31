<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permintaan extends Model
{
    use HasFactory;

    protected $table = 'permintaans'; 

    protected $fillable = [
        'user_id',
        'tgl_masuk',
        'no_hp',
        'metode_penyampaian',
        'jenis_permintaan',
        'detail_keluhan', // Kolom JSON baru
        'uraian',
        'unit_terkait',
        'tgl_verifikasi',
    ];

    // Otomatis ubah JSON ke Array dan sebaliknya
    protected $casts = [
        'detail_keluhan' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}