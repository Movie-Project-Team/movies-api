<?php

namespace App\Models\DB;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserNotification extends Model
{
    use HasFactory;

    protected $table = 'user_notification';
 
    protected $fillable = [
        'profile_id',
        'notification_id',
        'is_read'
    ];
}
