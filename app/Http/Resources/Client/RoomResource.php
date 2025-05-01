<?php

namespace App\Http\Resources\Client;

use App\Support\Helper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoomResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'room_code' => $this->room_code,
            'name' => $this->name,
            'is_locked' => (bool) $this->is_locked,
            'capacity' => $this->capacity,
            'status' => $this->status,
            'host' => new ProfileResource($this->host),
            'movie' => new MovieResource($this->movie),
            'created_at' => Helper::formatDate($this->created_at , 'd-m-Y H:i'),
        ];
    }
}
