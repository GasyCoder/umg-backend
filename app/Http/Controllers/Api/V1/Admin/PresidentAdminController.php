<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\PresidentResource;
use App\Models\President;
use App\Support\Audit;
use Illuminate\Http\Request;

class PresidentAdminController extends Controller
{
    public function index(Request $request)
    {
        $this->ensureRole($request);

        $presidents = President::with('photo')
            ->ordered()
            ->get();

        return PresidentResource::collection($presidents);
    }

    public function show(Request $request, int $id)
    {
        $this->ensureRole($request);

        $president = President::with('photo')->findOrFail($id);

        return new PresidentResource($president);
    }

    public function store(Request $request)
    {
        $this->ensureRole($request);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'title' => 'nullable|string|max:100',
            'mandate_start' => 'required|integer|min:1900|max:2100',
            'mandate_end' => 'nullable|integer|min:1900|max:2100|gte:mandate_start',
            'bio' => 'nullable|string',
            'photo_id' => 'nullable|exists:media,id',
            'is_current' => 'boolean',
            'order' => 'integer',
        ]);

        // Si marqué comme actuel, enlever le flag des autres
        if (!empty($data['is_current']) && $data['is_current']) {
            President::where('is_current', true)->update(['is_current' => false]);
        }

        // Calculer l'ordre si non fourni
        if (!isset($data['order'])) {
            $data['order'] = President::max('order') + 1;
        }

        $president = President::create($data);

        Audit::log($request, 'president.create', 'President', $president->id, [
            'name' => $president->name,
        ]);

        return new PresidentResource($president->load('photo'));
    }

    public function update(Request $request, int $id)
    {
        $this->ensureRole($request);

        $president = President::findOrFail($id);

        $data = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'title' => 'nullable|string|max:100',
            'mandate_start' => 'sometimes|required|integer|min:1900|max:2100',
            'mandate_end' => 'nullable|integer|min:1900|max:2100',
            'bio' => 'nullable|string',
            'photo_id' => 'nullable|exists:media,id',
            'is_current' => 'boolean',
            'order' => 'integer',
        ]);

        // Si marqué comme actuel, enlever le flag des autres
        if (!empty($data['is_current']) && $data['is_current']) {
            President::where('is_current', true)
                ->where('id', '!=', $id)
                ->update(['is_current' => false]);
        }

        $president->update($data);

        Audit::log($request, 'president.update', 'President', $president->id, [
            'name' => $president->name,
        ]);

        return new PresidentResource($president->load('photo'));
    }

    public function destroy(Request $request, int $id)
    {
        $this->ensureRole($request);

        $president = President::findOrFail($id);

        Audit::log($request, 'president.delete', 'President', $president->id, [
            'name' => $president->name,
        ]);

        $president->delete();

        return response()->json(['data' => true]);
    }

    public function reorder(Request $request)
    {
        $this->ensureRole($request);

        $data = $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:presidents,id',
            'items.*.order' => 'required|integer',
        ]);

        foreach ($data['items'] as $item) {
            President::where('id', $item['id'])->update(['order' => $item['order']]);
        }

        Audit::log($request, 'president.reorder', 'President', null, [
            'count' => count($data['items']),
        ]);

        return response()->json(['data' => true, 'message' => 'Ordre mis à jour']);
    }

    private function ensureRole(Request $request): void
    {
        abort_unless($request->user()?->hasAnyRole(['SuperAdmin', 'Validateur']), 403);
    }
}
