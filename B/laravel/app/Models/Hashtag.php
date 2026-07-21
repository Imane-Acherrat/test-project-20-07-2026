<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Hashtag extends Model
{
    protected $fillable = ['name'];

    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'hashtag_post');
    }


    public static function normalize(string $raw): string
    {
        $value = strtolower(trim($raw));
        $value = ltrim($value, '#');
        $value = preg_replace('/\s+/', '', $value);

        return $value;
    }

    public static function findOrCreateByName(string $raw): self
    {
        return static::firstOrCreate(['name' => static::normalize($raw)]);
    }
}
