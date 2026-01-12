<?php

namespace App\Http\Controllers\Api\V1\PublicApi;

use App\Http\Controllers\Controller;
use App\Http\Resources\ServiceResource;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $q = Service::query()
            ->with(['logo', 'document'])
            ->where('is_active', true)
            ->orderBy('order')
            ->orderBy('name');

        if ($request->filled('search')) {
            $search = $request->string('search');
            $q->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('chef_service', 'like', "%{$search}%");
            });
        }

        $per = min((int)$request->get('per_page', 20), 100);

        return ServiceResource::collection($q->paginate($per));
    }

    public function show(string $slug)
    {
        $service = Service::with(['logo', 'document'])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        return new ServiceResource($service);
    }
}
