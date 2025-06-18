<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Profile extends Model
{
    /** @use HasFactory<\Database\Factories\ProfileFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'gender',
        'tgllahir',
        'address',  // Kolom 'address' harus ada di sini
        'nip',
        'phone_number',
        'tglmasuk',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}