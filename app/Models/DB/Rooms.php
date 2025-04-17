<?php

namespace App\Models\DB;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rooms extends Model
{
    use HasFactory;

    protected $fillable = ['room_code', 'name', 'host_id', 'is_locked', 'password', 'thumbnail_url', 'movie_id', 'capacity', 'status'];

    public function host()
    {
        return $this->belongsTo(Profile::class, 'host_id');
    }

    public function movie()
    {
        return $this->belongsTo(Movies::class, 'movie_id');
    }
}
