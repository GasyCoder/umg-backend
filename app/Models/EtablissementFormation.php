<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EtablissementFormation extends Model
{
    protected $fillable = [
        'etablissement_id',
        'title',
        'level',
        'description',
        'order',
    ];

    public function etablissement()
    {
        return $this->belongsTo(Etablissement::class);
    }
}
