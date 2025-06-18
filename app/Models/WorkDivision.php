<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkDivision extends Model
{
    use HasFactory;

    // Tentukan kolom yang dapat diisi
    protected $fillable = [
        'detail_id',
        'division_id',
    ];

    /**
     * Relasi ke tabel detail (Inverse One-to-Many).
     */
    public function detail()
    {
        return $this->belongsTo(Detail::class);
    }

    /**
     * Relasi ke tabel division (Inverse One-to-Many).
     */
    public function division()
    {
        return $this->belongsTo(Division::class);
    }
}