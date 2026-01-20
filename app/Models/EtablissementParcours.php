<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EtablissementParcours extends Model
{
    protected $fillable = [
        'etablissement_id',
        'title',
        'mode',
        'description',
        'order',
    ];

    public function etablissement()
    {
        return $this->belongsTo(Etablissement::class);
    }
}
