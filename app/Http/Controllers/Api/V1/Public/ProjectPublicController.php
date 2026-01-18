<?php

namespace App\Http\Controllers\Api\V1\Public;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectPublicController extends Controller
{
    public function index(Request $request)
    {
        $query = Project::query()->active()->with('heroImage')->orderBy('title');

        return response()->json([
            'data' => $query->get()->map(fn(Project $project) => $this->formatProject($project)),
        ]);
    }

    public function show(string $slug)
    {
        $project = Project::query()
            ->active()
            ->with('heroImage')
            ->where('slug', $slug)
            ->firstOrFail();

        return response()->json([
            'data' => $this->formatProject($project),
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
            'updated_at' => $project->updated_at?->toISOString(),
        ];
    }
}

