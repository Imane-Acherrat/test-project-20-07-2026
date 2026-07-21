<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'username' => $this->username,
            'email' => $this->email,
            'bio' => $this->bio,
            'profileImage' => $this->profile_image
                ? asset('storage/'.$this->profile_image)
                : null,
            'postsCount' => $this->posts()->count(),
            'likesReceived' => $this->likesReceived(),
            'createdAt' => $this->created_at?->toIso8601String(),
        ];
    }
}
