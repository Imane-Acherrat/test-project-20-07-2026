<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HashtagResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'postsCount' => $this->when(
                isset($this->posts_count),
                fn () => $this->posts_count,
                fn () => $this->posts()->count()
            ),
        ];
    }
}
