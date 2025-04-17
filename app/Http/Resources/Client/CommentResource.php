<?php

namespace App\Http\Resources\Client;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
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
            'content' => $this->content,
            'status' => $this->is_approved,
            'parent' => new CommentResource($this->parent),
            'user' => new ProfileResource($this->profile),
            'created_at' => $this->created_at->diffForHumans(),
            'replies' => CommentResource::collection($this->whenLoaded('replies')),
        ];
    }
}
