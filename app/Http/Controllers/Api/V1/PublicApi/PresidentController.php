<?php

namespace App\Http\Controllers\Api\V1\PublicApi;

use App\Http\Controllers\Controller;
use App\Http\Resources\PresidentResource;
use App\Models\President;

class PresidentController extends Controller
{
    /**
     * Liste de tous les présidents/recteurs historiques
     */
    public function index()
    {
        $presidents = President::with('photo')
            ->ordered()
            ->get();

        return PresidentResource::collection($presidents);
    }

    /**
     * Obtenir le président/recteur actuel
     */
    public function current()
    {
        $president = President::with('photo')
            ->current()
            ->first();

        if (!$president) {
            return response()->json(['data' => null]);
        }

        return new PresidentResource($president);
    }
}
