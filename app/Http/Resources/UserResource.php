<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource {
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            // This resource's user, not Auth::user(); Gravatar hashes the trimmed,
            // lowercased address.
            'gravatar_url' => 'https://gravatar.com/avatar/' . hash('sha256', strtolower(trim($this->email))),
        ];
    }
}
