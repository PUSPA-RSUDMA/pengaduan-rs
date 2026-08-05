<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KategoriPermintaan extends Model
{
    protected $table = 'kategori_permintaan';
    protected $fillable = ['name'];

    public function items()
    {
        return $this->hasMany(ItemPermintaan::class, 'kategori_permintaan_id');
    }
}