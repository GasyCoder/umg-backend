<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProjectAdminController extends Controller
{
    public function index(Request $request)
    {
        $query = Project::query()->with('heroImage')->orderBy('title');

        if ($request->filled('is_active')) {
            $query->where('is_active', filter_var($request->get('is_active'), FILTER_VALIDATE_BOOLEAN));
        }

        $perPage = min((int) $request->get('per_page', 20), 50);

        return response()->json([
            'data' => $query->paginate($perPage)->through(fn(Project $project) => $this->formatProject($project)),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'slug' => 'nullable|string|max:120|unique:projects,slug',
            'kicker' => 'nullable|string|max:120',
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'meta' => 'nullable',
            'hero_image' => 'nullable|image|max:5120',
            'hero_image_id' => 'nullable|integer|exists:media,id',
            'is_active' => 'nullable|boolean',
        ]);

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['title']);
        }

        if ($request->hasFile('hero_image')) {
            $media = $this->uploadImage($request->file('hero_image'), $request->user()->id);
            $data['hero_image_id'] = $media->id;
        }
        unset($data['hero_image']);

        if (isset($data['meta']) && is_string($data['meta'])) {
            $decoded = json_decode($data['meta'], true);
            $data['meta'] = json_last_error() === JSON_ERROR_NONE ? $decoded : null;
        }

        $data['kicker'] = $data['kicker'] ?? 'Projets Internationale';
        $data['is_active'] = $data['is_active'] ?? true;
        $data['created_by'] = $request->user()->id;
        $data['updated_by'] = $request->user()->id;

        $project = Project::create($data);

        return response()->json([
            'data' => $this->formatProject($project->load('heroImage')),
            'message' => 'Projet créé avec succès',
        ], 201);
    }

    public function show(int $id)
    {
        $project = Project::with('heroImage')->findOrFail($id);

        return response()->json([
            'data' => $this->formatProject($project),
        ]);
    }

    public function update(Request $request, int $id)
    {
        $project = Project::findOrFail($id);

        $data = $request->validate([
            'slug' => "sometimes|required|string|max:120|unique:projects,slug,{$project->id}",
            'kicker' => 'nullable|string|max:120',
            'title' => 'sometimes|required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'meta' => 'nullable',
            'hero_image' => 'nullable|image|max:5120',
            'hero_image_id' => 'nullable|integer|exists:media,id',
            'is_active' => 'nullable|boolean',
        ]);

        if ($request->hasFile('hero_image')) {
            $media = $this->uploadImage($request->file('hero_image'), $request->user()->id);
            $data['hero_image_id'] = $media->id;
        }
        unset($data['hero_image']);

        if (isset($data['meta']) && is_string($data['meta'])) {
            $decoded = json_decode($data['meta'], true);
            $data['meta'] = json_last_error() === JSON_ERROR_NONE ? $decoded : null;
        }

        $data['updated_by'] = $request->user()->id;

        $project->fill($data)->save();

        return response()->json([
            'data' => $this->formatProject($project->load('heroImage')),
            'message' => 'Projet mis à jour avec succès',
        ]);
    }

    public function destroy(int $id)
    {
        $project = Project::findOrFail($id);
        $project->delete();

        return response()->json([
            'data' => true,
            'message' => 'Projet supprimé avec succès',
        ]);
    }

    private function formatProject(Project $project): array
    {
        return [
            'id' => $project->id,
            'slug' => $project->slug,
            'kicker' => $project->kicker,
            'title' => $project->title,
            'subtitle' => $project->subtitle,
            'description' => $project->description,
            'meta' => $project->meta,
            'hero_image_id' => $project->hero_image_id,
            'hero_image_url' => $project->hero_image_url,
            'is_active' => (bool) $project->is_active,
            'created_at' => $project->created_at?->toISOString(),
            'updated_at' => $project->updated_at?->toISOString(),
        ];
    }

    private function uploadImage($file, int $userId): Media
    {
        $disk = 'public';
        $folder = 'uploads/' . now()->format('Y/m');
        $filename = (string) Str::uuid() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs($folder, $filename, $disk);

        return Media::create([
            'disk' => $disk,
            'path' => $path,
            'name' => $file->getClientOriginalName(),
            'type' => 'file',
            'mime' => $file->getMimeType(),
            'size' => $file->getSize(),
            'alt' => 'Project hero image',
            'created_by' => $userId,
        ]);
    }
}
