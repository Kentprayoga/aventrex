<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'action',
        'detail',
        'ip_address',
        'user_agent',
    ];

    /**
     * Get the user that owns the log.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}