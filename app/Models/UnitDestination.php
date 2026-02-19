<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnitDestination extends Model
{
    use HasFactory;

    // Izin untuk isi kolom 'name' dan 'code'
    protected $fillable = ['name', 'code'];
}