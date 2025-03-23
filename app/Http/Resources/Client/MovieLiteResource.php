<?php

namespace App\Http\Resources\Client;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MovieLiteResource extends JsonResource
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
            'title' => $this->title,
            'name' => $this->name,
            'season' => $this->season,
            'lang' => $this->lang,
            'time' => $this->time,
            'esp_total' => $this->esp_total,
            'year' => $this->year,
            'slug' => $this->slug,
            'thumbnail' => $this->thumbnail,
            'poster' => $this->poster,
        ];
    }
}
