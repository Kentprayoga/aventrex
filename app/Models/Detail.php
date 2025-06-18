<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Detail extends Model
{
    use HasFactory;

    // Tentukan kolom yang dapat diisi
    protected $fillable = [
        'user_id',
        'position_id',
    ];

    /**
     * Relasi ke tabel user (Inverse One-to-Many).
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke tabel position (Inverse One-to-Many).
     */
    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    /**
     * Relasi ke tabel work_divisions (One-to-Many).
     */
    public function workDivisions()
    {
        return $this->hasMany(WorkDivision::class);
    }
}