<?php

namespace App\Models\DB;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use PHPUnit\TextUI\XmlConfiguration\MoveCoverageDirectoriesToSource;

class Genres extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'slug'
    ];

    public function movies(): BelongsToMany
    {
        return $this->belongsToMany(Movies::class, 'movie_genre', 'genre_id', 'movie_id');
    }
}
