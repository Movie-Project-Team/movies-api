<?php

namespace App\Models\DB;

use App\Models\Notification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Roles extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function admins(): BelongsToMany
    {
        return $this->belongsToMany(Admin::class, 'admin_role', 'role_id', 'admin_id');
    }

    public function notifications()
    {
        return $this->belongsToMany(Notification::class, 'user_notification', 'user_id', 'notification_id');
    }
}
