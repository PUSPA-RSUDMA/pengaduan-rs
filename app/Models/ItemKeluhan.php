<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemKeluhan extends Model
{
    protected $table = 'item_keluhan'; // Set nama tabel manual
    protected $fillable = ['kategori_keluhan_id', 'name'];
    
    public function kategori() {
        return $this->belongsTo(KategoriKeluhan::class, 'kategori_keluhan_id');
    }
}