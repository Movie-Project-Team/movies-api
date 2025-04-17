<?php

namespace App\Http\Resources\Client;

use App\Support\Helper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WatchHistoryResource extends JsonResource
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
            'profile' => new ProfileResource($this->profile),
            'movie' => new MovieLiteResource($this->movie),
            'timeProcess' => $this->time_process,
            'episode' => $this->episode,
            'lastWatchedAt' => Helper::formatDate($this->last_watched_at),
        ];
    }
}
