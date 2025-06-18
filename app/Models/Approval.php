<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Approval extends Model
{
        protected $fillable = [
        'document_id',
        'user_id',
        'status',
        'alasan',
    ];

    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}