<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class President extends Model
{
    protected $fillable = [
        'name',
        'title',
        'mandate_start',
        'mandate_end',
        'bio',
        'photo_id',
        'is_current',
        'order',
    ];

    protected $casts = [
        'mandate_start' => 'integer',
        'mandate_end' => 'integer',
        'is_current' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Relation avec Media pour la photo
     */
    public function photo(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'photo_id');
    }

    /**
     * Scope pour obtenir les présidents ordonnés
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderByDesc('mandate_start');
    }

    /**
     * Scope pour le président actuel
     */
    public function scopeCurrent($query)
    {
        return $query->where('is_current', true);
    }

    /**
     * Accessor pour la période de mandat formatée
     */
    public function getMandatePeriodAttribute(): string
    {
        if ($this->mandate_end) {
            return "{$this->mandate_start} - {$this->mandate_end}";
        }
        return "{$this->mandate_start} - présent";
    }
}
