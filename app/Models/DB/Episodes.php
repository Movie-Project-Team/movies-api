<?php

namespace App\Models\DB;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Episodes extends Model
{
    use HasFactory;

    protected $fillable = [
        'movie_id',
        'season',
        'title',
        'episode',
        'description',
        'release_date',
        'slug',
        'duration',
    ];

    public function movies(): BelongsTo
    {
        return $this->belongsTo(Movies::class, 'movie_id');
    }
}
