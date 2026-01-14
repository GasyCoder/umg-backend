<?php

namespace App\Http\Controllers\Api\V1\PublicApi;

use App\Http\Controllers\Controller;
use App\Models\Slide;
use Illuminate\Http\JsonResponse;

class SlidePublicController extends Controller
{
    public function index(): JsonResponse
    {
        $slides = Slide::with(['image', 'post', 'category'])
            ->active()
            ->ordered()
            ->get()
            ->map(fn($slide) => [
                'id' => $slide->id,
                'title' => $slide->title,
                'subtitle' => $slide->subtitle,
                'description' => $slide->description,
                'image_url' => $slide->image_url,
                'cta_text' => $slide->cta_text,
                'cta_url' => $slide->cta_url,
                'post' => $slide->post ? [
                    'id' => $slide->post->id,
                    'title' => $slide->post->title,
                    'slug' => $slide->post->slug,
                ] : null,
                'category' => $slide->category ? [
                    'id' => $slide->category->id,
                    'name' => $slide->category->name,
                    'slug' => $slide->category->slug,
                ] : null,
            ]);

        return response()->json([
            'data' => $slides,
        ]);
    }
}
