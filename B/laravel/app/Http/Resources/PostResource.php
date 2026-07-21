<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'description' => $this->description,
            'image' => asset('storage/'.$this->image),
            'hashtags' => $this->whenLoaded(
                'hashtags',
                fn () => $this->hashtags->pluck('name')->values(),
                []
            ),
            'likesCount' => $this->likes_count,
            'isLiked' => (bool) ($this->is_liked ?? false),
            'creator' => new PostCreatorResource($this->whenLoaded('creator')),
            'createdAt' => $this->created_at?->toIso8601String(),
            'updatedAt' => $this->updated_at?->toIso8601String(),
        ];
    }
}
