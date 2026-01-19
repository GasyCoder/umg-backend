<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Etab extends Model
{
    use SoftDeletes;

    protected $table = 'etabs';

    protected $fillable = [
        'name',
        'uuid',
        'type_id',
        'sigle',
        'director',
        'slogan',
        'about',
        'image_path',
        'status',
        'is_doctoral',
    ];

    protected $casts = [
        'type_id' => 'integer',
        'status' => 'boolean',
        'is_doctoral' => 'boolean',
    ];
}
