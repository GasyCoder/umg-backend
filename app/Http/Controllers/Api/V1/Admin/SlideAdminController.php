<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Slide;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SlideAdminController extends Controller
{
    public function index(Request $request)
    {
        $query = Slide::query()->with(['image', 'post', 'category']);

        if ($request->filled('is_active')) {
            $query->where('is_active', filter_var($request->get('is_active'), FILTER_VALIDATE_BOOLEAN));
        }

        $perPage = min((int) $request->get('per_page', 20), 50);

        return response()->json([
            'data' => $query->ordered()->paginate($perPage)->through(fn($slide) => $this->formatSlide($slide)),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:5120',
            'image_id' => 'nullable|integer|exists:media,id',
            'cta_text' => 'nullable|string|max:100',
            'cta_url' => 'nullable|string|max:500',
            'post_id' => 'nullable|integer|exists:posts,id',
            'category_id' => 'nullable|integer|exists:categories,id',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $media = $this->uploadImage($request->file('image'), $request->user()->id);
            $data['image_id'] = $media->id;
        }
        unset($data['image']);

        $data['created_by'] = $request->user()->id;
        $data['is_active'] = $data['is_active'] ?? true;
        $data['order'] = $data['order'] ?? Slide::max('order') + 1;

        $slide = Slide::create($data);

        return response()->json([
            'data' => $this->formatSlide($slide->load(['image', 'post', 'category'])),
            'message' => 'Slide créé avec succès',
        ], 201);
    }

    public function show(int $id)
    {
        $slide = Slide::with(['image', 'post', 'category'])->findOrFail($id);

        return response()->json([
            'data' => $this->formatSlide($slide),
        ]);
    }

    public function update(Request $request, int $id)
    {
        $slide = Slide::findOrFail($id);

        $data = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:5120',
            'image_id' => 'nullable|integer|exists:media,id',
            'cta_text' => 'nullable|string|max:100',
            'cta_url' => 'nullable|string|max:500',
            'post_id' => 'nullable|integer|exists:posts,id',
            'category_id' => 'nullable|integer|exists:categories,id',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $media = $this->uploadImage($request->file('image'), $request->user()->id);
            $data['image_id'] = $media->id;
        }
        unset($data['image']);

        $slide->fill($data)->save();

        return response()->json([
            'data' => $this->formatSlide($slide->load(['image', 'post', 'category'])),
            'message' => 'Slide mis à jour avec succès',
        ]);
    }

    public function destroy(int $id)
    {
        $slide = Slide::findOrFail($id);
        $slide->delete();

        return response()->json([
            'data' => true,
            'message' => 'Slide supprimé avec succès',
        ]);
    }

    public function reorder(Request $request)
    {
        $data = $request->validate([
            'slides' => 'required|array',
            'slides.*.id' => 'required|integer|exists:slides,id',
            'slides.*.order' => 'required|integer|min:0',
        ]);

        foreach ($data['slides'] as $item) {
            Slide::where('id', $item['id'])->update(['order' => $item['order']]);
        }

        return response()->json([
            'data' => true,
            'message' => 'Ordre mis à jour',
        ]);
    }

    private function formatSlide(Slide $slide): array
    {
        return [
            'id' => $slide->id,
            'title' => $slide->title,
            'subtitle' => $slide->subtitle,
            'description' => $slide->description,
            'image_id' => $slide->image_id,
            'image_url' => $slide->image_url,
            'cta_text' => $slide->cta_text,
            'cta_url' => $slide->cta_url,
            'post_id' => $slide->post_id,
            'post' => $slide->post ? [
                'id' => $slide->post->id,
                'title' => $slide->post->title,
                'slug' => $slide->post->slug,
            ] : null,
            'category_id' => $slide->category_id,
            'category' => $slide->category ? [
                'id' => $slide->category->id,
                'name' => $slide->category->name,
                'slug' => $slide->category->slug,
            ] : null,
            'order' => $slide->order,
            'is_active' => $slide->is_active,
            'created_at' => $slide->created_at?->toISOString(),
            'updated_at' => $slide->updated_at?->toISOString(),
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
            'mime' => $file->getMimeType(),
            'size' => $file->getSize(),
            'alt' => 'Slide image',
            'created_by' => $userId,
        ]);
    }
}
