<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterOutputPkrs extends Model
{
    use HasFactory;

    protected $table = 'master_output_pkrs';
    protected $fillable = ['name'];
}