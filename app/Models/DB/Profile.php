<?php

namespace App\Models\DB;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;

    protected $table = 'profile';
    
    protected $fillable = [
        'user_id',
        'name',
        'birthday',
        'gender',
        'phone',
        'password',
    ];

    public function favorites()
    {
        return $this->belongsToMany(Movies::class, 'favorite', 'profile_id', 'movie_id')->withTimestamps();
    }
}
