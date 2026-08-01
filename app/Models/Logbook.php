<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Logbook extends Model
{
    protected $fillable = ['judul_acara', 'deskripsi', 'tanggal_acara', 'status'];
}