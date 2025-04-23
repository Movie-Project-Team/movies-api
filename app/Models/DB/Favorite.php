<?php

namespace App\Models\DB;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    use HasFactory;

    protected $table = 'favorite';

    protected $fillable = [
        'movie_id',
        'profile_id',
    ];

    public function movie()
    {
        return $this->belongsTo(Movies::class, 'movie_id');
    }

    public function profile()
    {
        return $this->belongsTo(Profile::class, 'profile_id');
    }
}
