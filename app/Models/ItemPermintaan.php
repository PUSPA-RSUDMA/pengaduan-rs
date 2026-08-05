<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemPermintaan extends Model
{
    protected $table = 'item_permintaan';
    protected $fillable = ['kategori_permintaan_id', 'name'];

    public function kategori()
    {
        return $this->belongsTo(KategoriPermintaan::class, 'kategori_permintaan_id');
    }
}