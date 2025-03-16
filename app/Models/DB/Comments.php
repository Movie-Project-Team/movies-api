<?php

namespace App\Models\DB;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Comments extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'movie_id', 'content', 'parent_id', 'is_approved'];

    public function replies()
    {
        return $this->hasMany(Comments::class, 'parent_id')->with('replies');
    }

    public function movie()
    {
        return $this->belongsTo(Movies::class, 'movie_id');
    }

    public function user()
    {
        return $this->belongsTo(Profile::class, 'user_id', 'user_id');
    }

    public function parent()
    {
        return $this->belongsTo(Comments::class, 'parent_id');
    }
}
