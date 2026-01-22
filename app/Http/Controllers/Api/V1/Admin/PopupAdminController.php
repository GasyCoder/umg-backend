<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Popup;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PopupAdminController extends Controller
{
    /**
     * Liste tous les popups avec filtrage
     */
    public function index(Request $request): JsonResponse
    {
        $query = Popup::query()->with(['image', 'creator']);

        // Filtre par statut actif
        if ($request->filled('is_active')) {
            $query->where('is_active', filter_var($request->get('is_active'), FILTER_VALIDATE_BOOLEAN));
        }

        // Recherche par titre
        if ($request->filled('q')) {
            $term = $request->string('q')->toString();
            $query->where('title', 'like', "%{$term}%");
        }

        $perPage = min((int) $request->get('per_page', 20), 50);

        $paginated = $query->orderByDesc('priority')->orderByDesc('id')->paginate($perPage);

        return response()->json([
            'data' => $paginated->through(fn($popup) => $this->formatPopup($popup)),
            'meta' => [
                'current_page' => $paginated->currentPage(),
                'last_page' => $paginated->lastPage(),
                'per_page' => $paginated->perPage(),
                'total' => $paginated->total(),
            ],
        ]);
    }

    /**
     * Crée un nouveau popup
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'content_html' => 'nullable|string',
            'button_text' => 'nullable|string|max:100',
            'button_url' => 'nullable|string|max:500',
            'image_id' => 'nullable|integer|exists:media,id',
            'icon' => 'nullable|string|max:50',
            'icon_color' => 'nullable|string|max:50',
            'items' => 'nullable|array',
            'items.*.icon' => 'nullable|string|max:50',
            'items.*.icon_color' => 'nullable|string|max:50',
            'items.*.title' => 'required|string|max:255',
            'items.*.description' => 'nullable|string|max:500',
            'delay_ms' => 'nullable|integer|min:0|max:300000',
            'show_on_all_pages' => 'nullable|boolean',
            'target_pages' => 'nullable|array',
            'target_pages.*' => 'string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_active' => 'nullable|boolean',
            'priority' => 'nullable|integer|min:0|max:100',
        ]);

        $data['created_by'] = $request->user()->id;
        $data['is_active'] = $data['is_active'] ?? false;
        $data['delay_ms'] = $data['delay_ms'] ?? 0;
        $data['button_text'] = $data['button_text'] ?? 'J\'ai compris';
        $data['show_on_all_pages'] = $data['show_on_all_pages'] ?? true;
        $data['priority'] = $data['priority'] ?? 0;

        $popup = Popup::create($data);

        return response()->json([
            'data' => $this->formatPopup($popup->load(['image', 'creator'])),
            'message' => 'Popup créé avec succès',
        ], 201);
    }

    /**
     * Affiche un popup spécifique
     */
    public function show(int $id): JsonResponse
    {
        $popup = Popup::with(['image', 'creator', 'updater'])->findOrFail($id);

        return response()->json([
            'data' => $this->formatPopup($popup),
        ]);
    }

    /**
     * Met à jour un popup
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $popup = Popup::findOrFail($id);

        $data = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'content_html' => 'nullable|string',
            'button_text' => 'nullable|string|max:100',
            'button_url' => 'nullable|string|max:500',
            'image_id' => 'nullable|integer|exists:media,id',
            'icon' => 'nullable|string|max:50',
            'icon_color' => 'nullable|string|max:50',
            'items' => 'nullable|array',
            'items.*.icon' => 'nullable|string|max:50',
            'items.*.icon_color' => 'nullable|string|max:50',
            'items.*.title' => 'required|string|max:255',
            'items.*.description' => 'nullable|string|max:500',
            'delay_ms' => 'nullable|integer|min:0|max:300000',
            'show_on_all_pages' => 'nullable|boolean',
            'target_pages' => 'nullable|array',
            'target_pages.*' => 'string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_active' => 'nullable|boolean',
            'priority' => 'nullable|integer|min:0|max:100',
        ]);

        $data['updated_by'] = $request->user()->id;

        $popup->fill($data)->save();

        return response()->json([
            'data' => $this->formatPopup($popup->load(['image', 'creator', 'updater'])),
            'message' => 'Popup mis à jour avec succès',
        ]);
    }

    /**
     * Supprime un popup
     */
    public function destroy(int $id): JsonResponse
    {
        $popup = Popup::findOrFail($id);
        $popup->delete();

        return response()->json([
            'data' => true,
            'message' => 'Popup supprimé avec succès',
        ]);
    }

    /**
     * Active/désactive un popup
     */
    public function toggle(Request $request, int $id): JsonResponse
    {
        $popup = Popup::findOrFail($id);

        $popup->update([
            'is_active' => !$popup->is_active,
            'updated_by' => $request->user()->id,
        ]);

        return response()->json([
            'data' => $this->formatPopup($popup->load(['image', 'creator'])),
            'message' => $popup->is_active ? 'Popup activé' : 'Popup désactivé',
        ]);
    }

    /**
     * Duplique un popup
     */
    public function duplicate(Request $request, int $id): JsonResponse
    {
        $original = Popup::findOrFail($id);

        $copy = $original->replicate();
        $copy->title = $original->title . ' (copie)';
        $copy->is_active = false;
        $copy->created_by = $request->user()->id;
        $copy->updated_by = null;
        $copy->save();

        return response()->json([
            'data' => $this->formatPopup($copy->load(['image', 'creator'])),
            'message' => 'Popup dupliqué avec succès',
        ], 201);
    }

    /**
     * Formate un popup pour la réponse JSON
     */
    private function formatPopup(Popup $popup): array
    {
        return [
            'id' => $popup->id,
            'title' => $popup->title,
            'content_html' => $popup->content_html,
            'button_text' => $popup->button_text,
            'button_url' => $popup->button_url,
            'image_id' => $popup->image_id,
            'image_url' => $popup->image_url,
            'image' => $popup->image ? [
                'id' => $popup->image->id,
                'url' => $popup->image->url,
                'thumbnail_url' => $popup->image->thumbnail_url ?? $popup->image->url,
            ] : null,
            'icon' => $popup->icon,
            'icon_color' => $popup->icon_color,
            'items' => $popup->items,
            'delay_ms' => $popup->delay_ms,
            'show_on_all_pages' => $popup->show_on_all_pages,
            'target_pages' => $popup->target_pages,
            'start_date' => $popup->start_date?->toISOString(),
            'end_date' => $popup->end_date?->toISOString(),
            'is_active' => $popup->is_active,
            'is_in_period' => $popup->isInPeriod(),
            'priority' => $popup->priority,
            'creator' => $popup->creator ? [
                'id' => $popup->creator->id,
                'name' => $popup->creator->name,
            ] : null,
            'updater' => $popup->updater ? [
                'id' => $popup->updater->id,
                'name' => $popup->updater->name,
            ] : null,
            'created_at' => $popup->created_at?->toISOString(),
            'updated_at' => $popup->updated_at?->toISOString(),
        ];
    }
}
