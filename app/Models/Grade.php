<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    use HasFactory;

    // Izin untuk isi kolom nama, warna, dan waktu
    protected $fillable = ['name', 'color_class', 'sla_hours'];
}