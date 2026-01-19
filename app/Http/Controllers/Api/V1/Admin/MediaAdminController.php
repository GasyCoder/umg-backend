<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\MediaResource;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File as HttpFile;
use Illuminate\Http\UploadedFile;

class MediaAdminController extends Controller
{
    public function index(Request $request)
    {
        $per = min((int) $request->get('per_page', 50), 100);
        $q = Media::query()
            ->select(['id', 'disk', 'path', 'mime', 'size', 'alt', 'width', 'height', 'created_at', 'name', 'type', 'parent_id'])
            ->withCount([
                'children as children_count',
                'children as files_count' => function ($query) {
                    $query->where('type', 'file');
                },
                'children as folders_count' => function ($query) {
                    $query->where('type', 'folder');
                },
            ])
            ->withSum(
                [
                    'children as files_size' => function ($query) {
                        $query->where('type', 'file');
                    },
                ],
                'size'
            )
            ->latest();

        if ($entryType = $request->get('entry_type')) {
            if (in_array($entryType, ['file', 'folder'], true)) {
                $q->where('type', $entryType);
            }
        }

        if ($type = $request->get('type')) {
            $q->where(function ($query) use ($type) {
                $query->where('mime', 'like', $type . '%')
                    ->orWhere('type', 'folder');
            });
        }

        $parentId = $request->get('parent_id');
        if ($parentId === 'root' || !$parentId) {
            $q->whereNull('parent_id');
        } else {
            $q->where('parent_id', $parentId);
        }

        if ($categoryId = $request->get('category_id')) {
            $q->whereHas('categories', function ($query) use ($categoryId) {
                $query->where('categories.id', $categoryId);
            });
        }

        if ($search = trim((string) $request->get('q', ''))) {
            $q->where(function ($query) use ($search) {
                $like = '%' . $search . '%';
                $query->where('name', 'like', $like)
                    ->orWhere('path', 'like', $like)
                    ->orWhere('alt', 'like', $like)
                    ->orWhere('mime', 'like', $like);
            });
        }

        return MediaResource::collection($q->paginate($per));
    }

    public function show(int $id)
    {
        $media = Media::findOrFail($id);
        return new MediaResource($media);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            // Max size is in kilobytes; allow up to ~500MB for videos.
            'file' => ['required','file','max:512000'],
            'alt' => ['nullable','string','max:255'],
            'disk' => ['nullable','in:public,private'],
            'parent_id' => ['nullable', 'exists:media,id'],
            'category_ids' => ['nullable', 'array'],
            'category_ids.*' => ['exists:categories,id'],
        ]);

        $disk = $validated['disk'] ?? 'public';
        $file = $validated['file'];

        $folder = 'uploads/' . now()->format('Y/m');
        $filename = (string) Str::uuid() . '.' . $file->getClientOriginalExtension();
        $path = null;
        $size = $file->getSize();
        $mime = $file->getMimeType();

        if ($file instanceof UploadedFile && str_starts_with((string) $mime, 'image/')) {
            $optimized = $this->optimizeImage($file, $folder, $filename, $disk);
            if ($optimized) {
                $path = $optimized['path'];
                $size = $optimized['size'];
                $mime = $optimized['mime'];
            }
        }

        if (!$path) {
            $path = $file->storeAs($folder, $filename, $disk);
        }

        $media = Media::create([
            'disk' => $disk,
            'path' => $path,
            'name' => $file->getClientOriginalName(),
            'type' => 'file',
            'mime' => $mime,
            'size' => $size,
            'alt' => $validated['alt'] ?? null,
            'created_by' => $request->user()->id,
            'parent_id' => $validated['parent_id'] ?? null,
        ]);

        if (!empty($validated['category_ids'])) {
            $media->categories()->sync($validated['category_ids']);
        }

        return new MediaResource($media);
    }

    public function storeFolder(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'not_regex:/[\/\\\\]/'],
            'parent_id' => ['nullable', 'exists:media,id'],
        ]);

        $folder = Media::create([
            'name' => trim($validated['name']),
            'type' => 'folder',
            'disk' => 'public',
            'path' => '', // Folders don't have a path in the same way files do
            'created_by' => $request->user()->id,
            'parent_id' => $validated['parent_id'] ?? null,
        ]);

        return new MediaResource($folder);
    }

    public function update(Request $request, int $id)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'not_regex:/[\/\\\\]/'],
        ]);

        $media = Media::findOrFail($id);
        $media->update([
            'name' => trim($validated['name']),
        ]);

        return new MediaResource($media->fresh());
    }

    public function move(Request $request, int $id)
    {
        $validated = $request->validate([
            'parent_id' => ['nullable', 'exists:media,id'],
        ]);

        $media = Media::findOrFail($id);

        // Ensure we are not moving a folder into itself or its own children, which would create a loop.
        if ($validated['parent_id']) {
            $parent = Media::findOrFail($validated['parent_id']);
            if ($parent->type !== 'folder') {
                return response()->json(['message' => 'Destination must be a folder.'], 400);
            }
            if ($this->isMovingIntoItselfOrChild($media, $parent)) {
                return response()->json(['message' => 'Invalid move operation.'], 400);
            }
        }

        $media->update([
            'parent_id' => $validated['parent_id']
        ]);

        return new MediaResource($media);
    }

    public function copy(Request $request, int $id)
    {
        $validated = $request->validate([
            'parent_id' => ['nullable', 'exists:media,id'],
            'name' => ['nullable', 'string', 'max:255'],
        ]);

        $source = Media::with('categories')->findOrFail($id);
        if ($source->type !== 'file') {
            return response()->json(['message' => 'Only files can be copied.'], 400);
        }

        $parentId = $validated['parent_id'] ?? null;
        if ($parentId) {
            $parent = Media::findOrFail($parentId);
            if ($parent->type !== 'folder') {
                return response()->json(['message' => 'Destination must be a folder.'], 400);
            }
        }

        $disk = $source->disk;
        $sourcePath = (string) $source->path;
        if (!$sourcePath) {
            return response()->json(['message' => 'Source file path missing.'], 400);
        }

        $dir = pathinfo($sourcePath, PATHINFO_DIRNAME);
        $ext = pathinfo($sourcePath, PATHINFO_EXTENSION);
        $newFilename = (string) Str::uuid() . ($ext ? '.' . $ext : '');
        $newPath = ($dir && $dir !== '.') ? ($dir . '/' . $newFilename) : $newFilename;

        try {
            $ok = Storage::disk($disk)->copy($sourcePath, $newPath);
            if (!$ok) {
                return response()->json(['message' => 'Failed to copy file.'], 500);
            }
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Failed to copy file.'], 500);
        }

        $size = $source->size;
        try {
            $size = (int) Storage::disk($disk)->size($newPath);
        } catch (\Throwable $e) {
            // keep source size
        }

        $copy = Media::create([
            'disk' => $disk,
            'path' => $newPath,
            'name' => $validated['name'] ?? $source->name,
            'type' => 'file',
            'mime' => $source->mime,
            'size' => $size,
            'alt' => $source->alt,
            'width' => $source->width,
            'height' => $source->height,
            'created_by' => $request->user()->id,
            'parent_id' => $parentId,
        ]);

        if ($source->relationLoaded('categories')) {
            $copy->categories()->sync($source->categories->pluck('id')->all());
        }

        return new MediaResource($copy);
    }

    public function destroy(int $id)
    {
        $media = Media::findOrFail($id);

        if ($media->type === 'folder') {
            $hasChildren = Media::query()->where('parent_id', $media->id)->exists();
            if ($hasChildren) {
                return response()->json(['message' => 'Folder is not empty.'], 400);
            }
        }

        $media->delete();

        return response()->json(['data' => true]);
    }

    private function isMovingIntoItselfOrChild(Media $media, Media $parent)
    {
        if ($media->id === $parent->id) {
            return true;
        }
        $current = $parent;
        while ($current->parent_id) {
            if ($current->parent_id === $media->id) {
                return true;
            }
            $current = $current->parent;
        }
        return false;
    }

    private function optimizeImage(UploadedFile $file, string $folder, string $filename, string $disk): ?array
    {
        if (!extension_loaded('gd')) {
            return null;
        }

        $realPath = $file->getRealPath();
        if (!$realPath) {
            return null;
        }

        $info = @getimagesize($realPath);
        if (!$info || empty($info['mime'])) {
            return null;
        }

        $mime = $info['mime'];
        $create = match ($mime) {
            'image/jpeg' => 'imagecreatefromjpeg',
            'image/png' => 'imagecreatefrompng',
            'image/webp' => 'imagecreatefromwebp',
            default => null,
        };

        if (!$create || !function_exists($create)) {
            return null;
        }

        $src = @$create($realPath);
        if (!$src) {
            return null;
        }

        $width = imagesx($src);
        $height = imagesy($src);
        $maxDim = 2000;
        $scale = min(1, $maxDim / max($width, $height));
        $targetW = (int) max(1, round($width * $scale));
        $targetH = (int) max(1, round($height * $scale));

        $dst = imagecreatetruecolor($targetW, $targetH);
        if ($mime === 'image/png' || $mime === 'image/webp') {
            imagealphablending($dst, false);
            imagesavealpha($dst, true);
            $transparent = imagecolorallocatealpha($dst, 0, 0, 0, 127);
            imagefilledrectangle($dst, 0, 0, $targetW, $targetH, $transparent);
        }

        imagecopyresampled($dst, $src, 0, 0, 0, 0, $targetW, $targetH, $width, $height);

        $tmp = tempnam(sys_get_temp_dir(), 'umg_img_');
        if (!$tmp) {
            imagedestroy($src);
            imagedestroy($dst);
            return null;
        }

        $saved = match ($mime) {
            'image/jpeg' => imagejpeg($dst, $tmp, 82),
            'image/png' => imagepng($dst, $tmp, 7),
            'image/webp' => function_exists('imagewebp') ? imagewebp($dst, $tmp, 80) : false,
            default => false,
        };

        imagedestroy($src);
        imagedestroy($dst);

        if (!$saved) {
            @unlink($tmp);
            return null;
        }

        $storedPath = Storage::disk($disk)->putFileAs($folder, new HttpFile($tmp), $filename);
        $size = filesize($tmp) ?: $file->getSize();
        @unlink($tmp);

        return [
            'path' => $storedPath,
            'size' => $size,
            'mime' => $mime,
        ];
    }
}
