<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReporterType extends Model
{
    use HasFactory;
    
    // Izin untuk isi kolom 'name'
    protected $fillable = ['name'];
}