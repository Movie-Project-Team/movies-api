<?php

namespace App\Models\DB;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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

    public function servers(): BelongsToMany
    {
        return $this->belongsToMany(Server::class, 'server_episode', 'episode_id', 'server_id');
    }

    public function episodeServers()
    {
        return $this->hasMany(ServerEpisode::class, 'episode_id');
    }
}
