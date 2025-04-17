<?php

namespace App\Models\DB;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WatchHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'profile_id',
        'movie_id',
        'time_process',
        'episode',
        'last_watched_at',
    ];

    protected $casts = [
        'last_watched_at' => 'timestamp',
    ];

    public function profile()
    {
        return $this->belongsTo(Profile::class, 'profile_id', 'id');
    }

    public function movie()
    {
        return $this->belongsTo(Movies::class, 'movie_id', 'id');
    }
}
