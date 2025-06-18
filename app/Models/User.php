<?php


namespace App\Models;
use App\UserStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


class User extends Authenticatable
{
    use HasApiTokens,HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'email',
        'password',
        'role_id',
         'status'
    ];
        public function getStatusEnumAttribute(): UserStatus
    {
        return UserStatus::from($this->status);
    }
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected $casts = [
        'status' => 'string',  // Pastikan status adalah string, karena enum akan disimpan sebagai string
    ];

    // ✅ Query Scope: hanya user aktif

        public function setStatusAttribute($value)
    {
        // Konversikan status ke format yang benar (misalnya 'active' atau 'inactive')
        $this->attributes['status'] = strtolower($value);
    }    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function details()
    {
        return $this->hasMany(Detail::class);
    }

        public function isAdmin()
    {
        return $this->role_id === 1; // asumsi role_id 1 adalah admin
    }

public function messages()
{
    return $this->hasMany(Message::class, 'sender_id');
}

    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }
    public function leaveBalance()
{
    return $this->hasOne(LeaveBalance::class);
}
public function approvals()
{
    return $this->hasMany(Approval::class);
}

    public function getPositionName()
    {
        $detail = \App\Models\Detail::where('user_id', $this->id)->first();
        if ($detail) {
            $position = \App\Models\Position::find($detail->position_id);
            return $position ? $position->name : '-';
        }
        return '-';
    }

    public function getDivisionName()
    {
        $detail = \App\Models\Detail::where('user_id', $this->id)->first();
        if ($detail) {
            $workDivision = \App\Models\WorkDivision::where('detail_id', $detail->id)->first();
            if ($workDivision) {
                $division = \App\Models\Division::find($workDivision->division_id);
                return $division ? $division->name : '-';
            }
        }
        return '-';
    }


}