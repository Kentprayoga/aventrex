<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'roles';
    protected $fillable = ['name'];  // Kolom yang boleh diisi (name untuk role)

    // Relasi ke User (one-to-many)
    public function users()
    {
        return $this->hasMany(User::class);
    }
}