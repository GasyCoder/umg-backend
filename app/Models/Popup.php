<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class Popup extends Model
{
    protected $fillable = [
        'title',
        'content_html',
        'button_text',
        'button_url',
        'image_id',
        'icon',
        'icon_color',
        'items',
        'delay_ms',
        'show_on_all_pages',
        'target_pages',
        'start_date',
        'end_date',
        'is_active',
        'priority',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'items' => 'array',
        'target_pages' => 'array',
        'is_active' => 'boolean',
        'show_on_all_pages' => 'boolean',
        'delay_ms' => 'integer',
        'priority' => 'integer',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    protected $appends = ['image_url'];

    // =========================================================================
    // Relations
    // =========================================================================

    public function image(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'image_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // =========================================================================
    // Accessors
    // =========================================================================

    public function getImageUrlAttribute(): ?string
    {
        return $this->image?->url;
    }

    // =========================================================================
    // Scopes
    // =========================================================================

    /**
     * Scope pour les popups actifs et dans la période valide
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope pour les popups dans leur période de validité
     */
    public function scopeInPeriod(Builder $query): Builder
    {
        $now = now();

        return $query->where(function ($q) use ($now) {
            $q->whereNull('start_date')
              ->orWhere('start_date', '<=', $now);
        })->where(function ($q) use ($now) {
            $q->whereNull('end_date')
              ->orWhere('end_date', '>=', $now);
        });
    }

    /**
     * Scope pour obtenir le popup actif courant (le plus prioritaire)
     */
    public function scopeCurrentActive(Builder $query): Builder
    {
        return $query->active()
                     ->inPeriod()
                     ->orderByDesc('priority')
                     ->orderByDesc('id');
    }

    /**
     * Scope pour filtrer par page cible
     */
    public function scopeForPage(Builder $query, string $page): Builder
    {
        return $query->where(function ($q) use ($page) {
            $q->where('show_on_all_pages', true)
              ->orWhereJsonContains('target_pages', $page);
        });
    }

    // =========================================================================
    // Méthodes
    // =========================================================================

    /**
     * Vérifie si le popup est actuellement dans sa période de validité
     */
    public function isInPeriod(): bool
    {
        $now = now();

        if ($this->start_date && $now->lt($this->start_date)) {
            return false;
        }

        if ($this->end_date && $now->gt($this->end_date)) {
            return false;
        }

        return true;
    }

    /**
     * Vérifie si le popup doit s'afficher sur une page donnée
     */
    public function shouldShowOnPage(string $page): bool
    {
        if ($this->show_on_all_pages) {
            return true;
        }

        $targets = $this->target_pages ?? [];
        return in_array($page, $targets, true);
    }
}
