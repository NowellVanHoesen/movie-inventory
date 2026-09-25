<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MovieResource extends JsonResource {
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'overview' => $this->overview,
            'release_date' => $this->release_date,
            'purchase_date' => $this->purchase_date,
            'release_year' => $this->release_date ? date('Y', strtotime($this->release_date)) : 'TBD',
            'runtime' => $this->runtime,
            'poster_path' => $this->poster_path,
            'genres' => GenreResource::collection($this->whenLoaded('genres')),
            'cast_members' => CastMembersResource::collection($this->whenLoaded('castMembers')),
            'character' => $this->when($this->getRawOriginal('pivot_character'), $this->getRawOriginal('pivot_character')),
            'certification' => $this->certification->name,
            'collection' => new MovieCollectionResource($this->whenLoaded('collection')),
            // 'delete_link' => route('movies.destroy', $this->id),
        ];
    }
}
