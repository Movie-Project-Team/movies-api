<?php

namespace App\Models\DB;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Server extends Model
{
    use HasFactory;

    protected $table = 'server';
    
    protected $fillable = [
        'name',
        'kind'
    ];

    public function episodes(): BelongsToMany
    {
        return $this->belongsToMany(Episodes::class, 'server_episode', 'server_id', 'episode_id');
    }

    public function episodeServers()
    {
        return $this->hasMany(ServerEpisode::class);
    }
}