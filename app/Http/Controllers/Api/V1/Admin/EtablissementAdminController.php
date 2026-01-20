<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\EtablissementResource;
use App\Models\Etablissement;
use App\Support\Audit;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class EtablissementAdminController extends Controller
{
    public function index(Request $request)
    {
        $this->ensureRole($request);

        $q = Etablissement::query()
            ->with(['logo', 'coverImage', 'formations', 'parcours', 'doctoralTeams'])
            ->orderBy('order')
            ->orderBy('name');

        if ($request->filled('search')) {
            $search = $request->string('search');
            $q->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('acronym', 'like', "%{$search}%");
            });
        }

        if ($request->boolean('active_only')) {
            $q->where('is_active', true);
        }

        $per = min((int)$request->get('per_page', 20), 100);

        return EtablissementResource::collection($q->paginate($per));
    }

    public function show(Request $request, int $id)
    {
        $this->ensureRole($request);

        $etablissement = Etablissement::with(['logo', 'coverImage', 'formations', 'parcours', 'doctoralTeams'])->findOrFail($id);

        return new EtablissementResource($etablissement);
    }

    public function store(Request $request)
    {
        $this->ensureRole($request);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'acronym' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'director_name' => 'nullable|string|max:255',
            'director_title' => 'nullable|string|max:100',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'facebook' => 'nullable|string|max:255',
            'twitter' => 'nullable|string|max:255',
            'linkedin' => 'nullable|string|max:255',
            'logo_id' => 'nullable|exists:media,id',
            'cover_image_id' => 'nullable|exists:media,id',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'formations' => 'array',
            'formations.*.title' => 'required_with:formations|string|max:255',
            'formations.*.level' => 'nullable|string|max:100',
            'formations.*.description' => 'nullable|string',
            'parcours' => 'array',
            'parcours.*.title' => 'required_with:parcours|string|max:255',
            'parcours.*.mode' => 'nullable|string|max:100',
            'parcours.*.description' => 'nullable|string',
            'doctoral_teams' => 'array',
            'doctoral_teams.*.name' => 'required_with:doctoral_teams|string|max:255',
            'doctoral_teams.*.focus' => 'nullable|string',
        ]);

        $formations = Arr::pull($data, 'formations', []);
        $parcours = Arr::pull($data, 'parcours', []);
        $doctoralTeams = Arr::pull($data, 'doctoral_teams', []);

        $data['slug'] = Str::slug($data['name']);
        
        // Ensure unique slug
        $baseSlug = $data['slug'];
        $counter = 1;
        while (Etablissement::where('slug', $data['slug'])->exists()) {
            $data['slug'] = $baseSlug . '-' . $counter++;
        }

        $etablissement = Etablissement::create($data);

        $this->syncFormations($etablissement, $formations);
        $this->syncParcours($etablissement, $parcours);
        $this->syncDoctoralTeams($etablissement, $doctoralTeams);

        Audit::log($request, 'etablissement.create', 'Etablissement', $etablissement->id, [
            'name' => $etablissement->name,
        ]);

        return new EtablissementResource($etablissement->load(['logo', 'coverImage']));
    }

    public function update(Request $request, int $id)
    {
        $this->ensureRole($request);

        $etablissement = Etablissement::findOrFail($id);

        $data = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'acronym' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'director_name' => 'nullable|string|max:255',
            'director_title' => 'nullable|string|max:100',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'facebook' => 'nullable|string|max:255',
            'twitter' => 'nullable|string|max:255',
            'linkedin' => 'nullable|string|max:255',
            'logo_id' => 'nullable|exists:media,id',
            'cover_image_id' => 'nullable|exists:media,id',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'formations' => 'array',
            'formations.*.title' => 'required_with:formations|string|max:255',
            'formations.*.level' => 'nullable|string|max:100',
            'formations.*.description' => 'nullable|string',
            'parcours' => 'array',
            'parcours.*.title' => 'required_with:parcours|string|max:255',
            'parcours.*.mode' => 'nullable|string|max:100',
            'parcours.*.description' => 'nullable|string',
            'doctoral_teams' => 'array',
            'doctoral_teams.*.name' => 'required_with:doctoral_teams|string|max:255',
            'doctoral_teams.*.focus' => 'nullable|string',
        ]);

        $formations = Arr::pull($data, 'formations', []);
        $parcours = Arr::pull($data, 'parcours', []);
        $doctoralTeams = Arr::pull($data, 'doctoral_teams', []);

        // Update slug if name changed
        if (isset($data['name']) && $data['name'] !== $etablissement->name) {
            $data['slug'] = Str::slug($data['name']);
            $baseSlug = $data['slug'];
            $counter = 1;
            while (Etablissement::where('slug', $data['slug'])->where('id', '!=', $id)->exists()) {
                $data['slug'] = $baseSlug . '-' . $counter++;
            }
        }

        $etablissement->update($data);

        $this->syncFormations($etablissement, $formations);
        $this->syncParcours($etablissement, $parcours);
        $this->syncDoctoralTeams($etablissement, $doctoralTeams);

        Audit::log($request, 'etablissement.update', 'Etablissement', $etablissement->id, [
            'name' => $etablissement->name,
        ]);

        return new EtablissementResource($etablissement->load(['logo', 'coverImage']));
    }

    public function destroy(Request $request, int $id)
    {
        $this->ensureRole($request);

        $etablissement = Etablissement::findOrFail($id);

        Audit::log($request, 'etablissement.delete', 'Etablissement', $etablissement->id, [
            'name' => $etablissement->name,
        ]);

        $etablissement->delete();

        return response()->json(['data' => true]);
    }

    private function ensureRole(Request $request): void
    {
        abort_unless($request->user()?->hasAnyRole(['SuperAdmin', 'Validateur']), 403);
    }

    private function syncFormations(Etablissement $etablissement, array $formations): void
    {
        $etablissement->formations()->delete();
        foreach ($formations as $index => $formation) {
            if (empty($formation['title'])) {
                continue;
            }
            $etablissement->formations()->create([
                'title' => $formation['title'],
                'level' => $formation['level'] ?? null,
                'description' => $formation['description'] ?? null,
                'order' => $formation['order'] ?? $index,
            ]);
        }
    }

    private function syncParcours(Etablissement $etablissement, array $parcours): void
    {
        $etablissement->parcours()->delete();
        foreach ($parcours as $index => $entry) {
            if (empty($entry['title'])) {
                continue;
            }
            $etablissement->parcours()->create([
                'title' => $entry['title'],
                'mode' => $entry['mode'] ?? null,
                'description' => $entry['description'] ?? null,
                'order' => $entry['order'] ?? $index,
            ]);
        }
    }

    private function syncDoctoralTeams(Etablissement $etablissement, array $teams): void
    {
        $etablissement->doctoralTeams()->delete();
        foreach ($teams as $index => $team) {
            if (empty($team['name'])) {
                continue;
            }
            $etablissement->doctoralTeams()->create([
                'name' => $team['name'],
                'discipline' => $team['discipline'] ?? null,
                'contact' => $team['contact'] ?? null,
                'email' => $team['email'] ?? null,
                'focus' => $team['focus'] ?? null,
                'order' => $team['order'] ?? $index,
            ]);
        }
    }
}
