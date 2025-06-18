<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Division extends Model
{
    use HasFactory;

    // Tentukan kolom yang dapat diisi
    public $incrementing = false; // Non auto-increment
    protected $keyType = 'string'; // Jika ID adalah string (atau bisa gunakan integer manual)

    protected $fillable = ['id', 'name'];

    /**
     * Relasi ke tabel work_divisions (One-to-Many).
     */
    public function workDivisions()
    {
        return $this->hasMany(WorkDivision::class);
    }
}