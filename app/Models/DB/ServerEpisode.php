<?php

namespace App\Models\DB;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServerEpisode extends Model
{
    use HasFactory;
    
    protected $table = 'server_episode';

    protected $fillable = [
        'episode_id',
        'server_id',
        'name',
        'slug',
        'filename',
        'link_download',
        'link_watch',
    ];

    public function episode()
    {
        return $this->belongsTo(Episodes::class, 'episode_id');
    }

    public function server()
    {
        return $this->belongsTo(Server::class, 'server_id');
    }
}