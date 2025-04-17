<?php

namespace App\Models\DB;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Notifications extends Model
{
    use HasFactory;

    protected $table = 'notifications';
    protected $fillable = [
        'title',
        'message',
        'type',
        'link'
    ];

    public function profiles(): BelongsToMany
    {
        return $this->belongsToMany(Profile::class, 'user_notification', 'notification_id', 'profile_id');
    }
}
