<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Source extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    // Satu Sumber (Source) bisa punya BANYAK Keluhan (Complaints)
    public function complaints()
    {
        return $this->hasMany(Complaint::class);
    }
}