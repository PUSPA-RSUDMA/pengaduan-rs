<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KategoriKeluhan extends Model
{
    protected $table = 'kategori_keluhan'; // Set nama tabel manual
    protected $fillable = ['name'];
    
    public function items() {
        return $this->hasMany(ItemKeluhan::class, 'kategori_keluhan_id');
    }
}