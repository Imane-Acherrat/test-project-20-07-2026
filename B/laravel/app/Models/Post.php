<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'description',
        'image',
        'likes_count',
    ];

    protected $casts = [
        'likes_count' => 'integer',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function hashtags(): BelongsToMany
    {
        return $this->belongsToMany(Hashtag::class, 'hashtag_post');
    }

    public function likes(): HasMany
    {
        return $this->hasMany(Like::class);
    }

    public function getImageUrlAttribute(): string
    {
        return asset('storage/'.$this->image);
    }

    public function scopeWithIsLikedBy($query, ?int $userId)
    {
        return $query->addSelect('posts.*')->selectRaw(
            $userId
                ? 'exists(select 1 from likes where likes.post_id = posts.id and likes.user_id = ?) as is_liked'
                : '0 as is_liked',
            $userId ? [$userId] : []
        );
    }
}
