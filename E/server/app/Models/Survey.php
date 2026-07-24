<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Survey extends Model
{
    protected $fillable = [
        'user_id', 'title', 'description', 'status',
        'public_slug', 'confirmation_message'
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function ($survey) {
            if (empty($survey->public_slug)) {
                $survey->public_slug = (string) Str::uuid();
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class)->orderBy('order');
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class);
    }

    public function webhooks(): HasMany
    {
        return $this->hasMany(Webhook::class);
    }
}
