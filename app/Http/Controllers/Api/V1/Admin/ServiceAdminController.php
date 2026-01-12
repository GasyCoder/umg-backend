<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ServiceResource;
use App\Models\Service;
use App\Support\Audit;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ServiceAdminController extends Controller
{
    public function index(Request $request)
    {
        $this->ensureRole($request);

        $q = Service::query()
            ->with(['logo', 'document'])
            ->orderBy('order')
            ->orderBy('name');

        if ($request->filled('search')) {
            $search = $request->string('search');
            $q->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('chef_service', 'like', "%{$search}%");
            });
        }

        if ($request->boolean('active_only')) {
            $q->where('is_active', true);
        }

        $per = min((int)$request->get('per_page', 20), 100);

        return ServiceResource::collection($q->paginate($per));
    }

    public function show(Request $request, int $id)
    {
        $this->ensureRole($request);

        $service = Service::with(['logo', 'document'])->findOrFail($id);

        return new ServiceResource($service);
    }

    public function store(Request $request)
    {
        $this->ensureRole($request);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'chef_service' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'contact' => 'nullable|string|max:255',
            'logo_id' => 'nullable|exists:media,id',
            'document_id' => 'nullable|exists:documents,id',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $data['slug'] = Str::slug($data['name']);
        
        // Ensure unique slug
        $baseSlug = $data['slug'];
        $counter = 1;
        while (Service::where('slug', $data['slug'])->exists()) {
            $data['slug'] = $baseSlug . '-' . $counter++;
        }

        $service = Service::create($data);

        Audit::log($request, 'service.create', 'Service', $service->id, [
            'name' => $service->name,
        ]);

        return new ServiceResource($service->load(['logo', 'document']));
    }

    public function update(Request $request, int $id)
    {
        $this->ensureRole($request);

        $service = Service::findOrFail($id);

        $data = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'chef_service' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'contact' => 'nullable|string|max:255',
            'logo_id' => 'nullable|exists:media,id',
            'document_id' => 'nullable|exists:documents,id',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        // Update slug if name changed
        if (isset($data['name']) && $data['name'] !== $service->name) {
            $data['slug'] = Str::slug($data['name']);
            $baseSlug = $data['slug'];
            $counter = 1;
            while (Service::where('slug', $data['slug'])->where('id', '!=', $id)->exists()) {
                $data['slug'] = $baseSlug . '-' . $counter++;
            }
        }

        $service->update($data);

        Audit::log($request, 'service.update', 'Service', $service->id, [
            'name' => $service->name,
        ]);

        return new ServiceResource($service->load(['logo', 'document']));
    }

    public function destroy(Request $request, int $id)
    {
        $this->ensureRole($request);

        $service = Service::findOrFail($id);

        Audit::log($request, 'service.delete', 'Service', $service->id, [
            'name' => $service->name,
        ]);

        $service->delete();

        return response()->json(['data' => true]);
    }

    private function ensureRole(Request $request): void
    {
        abort_unless($request->user()?->hasAnyRole(['SuperAdmin', 'Validateur']), 403);
    }
}
