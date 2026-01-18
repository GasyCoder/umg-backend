<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Project extends Model
{
    protected $fillable = [
        'slug',
        'kicker',
        'title',
        'subtitle',
        'description',
        'meta',
        'hero_image_id',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'meta' => 'array',
        'is_active' => 'boolean',
    ];

    protected $appends = ['hero_image_url'];

    public function heroImage(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'hero_image_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function editor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function getHeroImageUrlAttribute(): ?string
    {
        return $this->heroImage?->url;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}

