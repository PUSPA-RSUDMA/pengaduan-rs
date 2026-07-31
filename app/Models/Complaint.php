<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'date', 'reporter_type', 'reporter_name',
        'source_id', 'detail_keluhan', 'description', 'answer',
        'unit_destination', 'grade', 'status',
    ];

    // Cast kolom json otomatis
    protected $casts = [
        'detail_keluhan' => 'array',
    ];

    public function user() { return $this->belongsTo(User::class); }
    public function source() { return $this->belongsTo(Source::class); }
}