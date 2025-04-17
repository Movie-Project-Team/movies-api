<?php

namespace App\Models\DB;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Models\Notification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'email',
        'password',
        'permission',
        'is_active'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function history(): HasOne
    {
        return $this->hasOne(History::class, 'user_id');
    }

    public function favourite(): HasOne
    {
        return $this->hasOne(Favourite::class, 'user_id');
    }

    public function notification(): BelongsToMany
    {
        return $this->belongsToMany(Notification::class, 'user_notification', 'user_id', 'notification_id');
    }
}
