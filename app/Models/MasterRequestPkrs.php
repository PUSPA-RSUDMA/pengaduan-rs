<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterRequestPkrs extends Model
{
    use HasFactory;

    protected $table = 'master_request_pkrs';
    protected $fillable = ['name'];
}