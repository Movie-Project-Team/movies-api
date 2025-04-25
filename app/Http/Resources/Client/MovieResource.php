<?php

namespace App\Http\Resources\Client;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MovieResource extends JsonResource
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
            'description' => $this->description,
            'produce_by' => $this->produce_by,
            'season' => $this->season,
            'lang' => $this->lang,
            'esp_total' => $this->esp_total,
            'esp_current' => $this->esp_current,
            'imdb' => $this->IMDb,
            'type' => $this->type, 
            'year' => $this->year,
            'view' => $this->view,
            'slug' => $this->slug,
            'thumbnail' => $this->thumbnail,
            'poster' => $this->poster,
            'languages' => LanguageResource::collection($this->languages)->toArray($request),
            'genres' => GenreResource::collection($this->genres)->toArray($request),
            'episodes' => ServerResource::collection($this->servers_grouped_by_name)
        ];
    }
}
