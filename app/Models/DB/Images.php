<?php

namespace App\Models\DB;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use PHPUnit\TextUI\XmlConfiguration\MoveAttributesFromFilterWhitelistToCoverage;

class Images extends Model
{
    use HasFactory;

    protected $fillable = [
        'alt',
        'url',
    ];

    public function movies(): BelongsToMany
    {
        return $this->belongsToMany(Movies::class, 'movie_image', 'image_id', 'movie_id');
    }
}
