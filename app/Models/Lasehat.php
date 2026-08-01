<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lasehat extends Model
{
    protected $fillable = [
        'nama_pasien', 'tempat_dirawat', 'alamat_tujuan', 
        'tanggal_pengantaran', 'penanggung_jawab', 'no_telp_pj', 
        'supir_ambulance', 'created_by'
    ];
}