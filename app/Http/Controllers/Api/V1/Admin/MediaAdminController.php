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
            ->select(['id', 'disk', 'path', 'mime', 'size', 'alt', 'width', 'height', 'created_at'])
            ->latest();

        if ($type = $request->get('type')) {
            $q->where('mime', 'like', $type . '%');
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
            'mime' => $mime,
            'size' => $size,
            'alt' => $validated['alt'] ?? null,
            'created_by' => $request->user()->id,
        ]);

        return new MediaResource($media);
    }

    public function destroy(int $id)
    {
        $media = Media::findOrFail($id);
        $media->delete();

        return response()->json(['data' => true]);
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
