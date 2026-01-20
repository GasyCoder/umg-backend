<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Etablissement extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'acronym',
        'description',
        'director_name',
        'director_title',
        'address',
        'phone',
        'email',
        'website',
        'facebook',
        'twitter',
        'linkedin',
        'logo_id',
        'cover_image_id',
        'order',
        'is_active',
        'is_doctoral',
        'uuid',
        'type_id',
        'sigle',
        'director',
        'slogan',
        'about',
        'image_path',
        'status',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_doctoral' => 'boolean',
        'status' => 'boolean',
        'order' => 'integer',
    ];

    public function logo(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'logo_id');
    }

    public function coverImage(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'cover_image_id');
    }

    public function formations(): HasMany
    {
        return $this->hasMany(EtablissementFormation::class);
    }

    public function parcours(): HasMany
    {
        return $this->hasMany(EtablissementParcours::class);
    }

    public function doctoralTeams(): HasMany
    {
        return $this->hasMany(DoctoralTeam::class);
    }
}
