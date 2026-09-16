<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SeriesResource extends JsonResource {
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'overview' => $this->overview,
            'first_air_date' => $this->first_air_date,
            'purchase_date' => $this->purchase_date,
            'release_year' => $this->first_air_date ? date('Y', strtotime($this->first_air_date)) : 'TBD',
            'poster_path' => $this->poster_path,
            'genres' => GenreResource::collection($this->whenLoaded('genres')),
            'cast_members' => CastMembersResource::collection($this->whenLoaded('cast_members')),
            'character' => $this->when($this->getRawOriginal('pivot_character'), $this->getRawOriginal('pivot_character')),
            'certification' => $this->certification->name,
        ];
    }
}
