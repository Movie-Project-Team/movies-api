<?php

namespace App\Models\DB;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comments extends Model
{
    use HasFactory;

    protected $fillable = ['profile_id', 'movie_id', 'content', 'parent_id', 'is_approved'];

    public function replies()
    {
        return $this->hasMany(Comments::class, 'parent_id')->with('replies');
    }

    public function movie()
    {
        return $this->belongsTo(Movies::class, 'movie_id');
    }

    public function profile()
    {
        return $this->belongsTo(Profile::class, 'profile_id');
    }

    public function parent()
    {
        return $this->belongsTo(Comments::class, 'parent_id');
    }
}
