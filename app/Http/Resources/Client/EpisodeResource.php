<?php

namespace App\Http\Resources\Client;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EpisodeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => (int) $this->id,
            'name' => $this->episode->episodeServers[0]->name ?? null,
            'slug' => $this->episode->episodeServers[0]->slug ?? null,
            'filename' => $this->episode->episodeServers[0]->filename ?? null,
            'link_embed' => $this->episode->episodeServers[0]->link_watch ?? null,
            'link_m3u8' => $this->episode->episodeServers[0]->link_download ?? null,
        ];
    }
}
