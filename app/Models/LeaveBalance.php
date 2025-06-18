<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveBalance extends Model
{
    protected $fillable = [
        'user_id',
        'total_leave',
        'used_leave',
        'remaining_leave',
    ];
    protected $casts = [
        'total_leave' => 'integer',
        'used_leave' => 'integer',
        'remaining_leave' => 'integer',
    ];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    use \Illuminate\Database\Eloquent\Factories\HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'leave_balances';
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }


}