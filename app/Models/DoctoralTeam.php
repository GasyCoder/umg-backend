<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DoctoralTeam extends Model
{
    protected $fillable = [
        'etablissement_id',
        'name',
        'focus',
        'order',
    ];

    public function etablissement()
    {
        return $this->belongsTo(Etablissement::class);
    }
}
