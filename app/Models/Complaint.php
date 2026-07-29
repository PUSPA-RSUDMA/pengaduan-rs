<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'reporter_type',
        'reporter_name',
        'source_id',
        'keluhan_sdm',
        'keluhan_sarpras',
        'keluhan_administrasi',
        'keluhan_farmasi',
        'keluhan_gizi',
        'keluhan_keamanan',
        'description',
        'answer',
        'unit_destination',
        'grade',
        'status',
    ];

    // Memberitahu Laravel untuk meng-cast (mengubah) data array checkbox menjadi format JSON otomatis ke Database
    protected $casts = [
        'keluhan_sdm' => 'array',
        'keluhan_sarpras' => 'array',
        'keluhan_administrasi' => 'array',
        'keluhan_farmasi' => 'array',
        'keluhan_gizi' => 'array',
        'keluhan_keamanan' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function source()
    {
        return $this->belongsTo(Source::class);
    }
}