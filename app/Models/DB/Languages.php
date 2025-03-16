<?php

namespace App\Models\DB;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Languages extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'code',
        'slug'
    ];

    public function movies(): BelongsToMany
    {
        return $this->belongsToMany(Movies::class, 'movie_language', 'movie_id', 'language_id');
    }
}
