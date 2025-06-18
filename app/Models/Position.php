<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    use HasFactory;

    // Tentukan kolom yang dapat diisi
    public $incrementing = false; // Non auto-increment
    protected $keyType = 'string'; // Kalau ID-nya huruf/angka manual

    protected $fillable = ['id', 'name'];

    /**
     * Relasi ke tabel details (One-to-Many).
     */
    public function details()
    {
        return $this->hasMany(Detail::class);
    }
}