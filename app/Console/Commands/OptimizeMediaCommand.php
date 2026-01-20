<?php

namespace App\Console\Commands;

use App\Models\Media;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class OptimizeMediaCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'media:optimize-all {--force : Force re-optimization even if already WebP}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Optimize all existing images (convert to WebP, resize, compress)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!extension_loaded('gd')) {
            $this->error('GD extension is not loaded.');
            return 1;
        }

        $query = Media::query()
            ->where('type', 'file')
            ->where(function ($q) {
                $q->where('mime', 'like', 'image/jpeg')
                  ->orWhere('mime', 'like', 'image/png')
                  ->orWhere('mime', 'like', 'image/webp');
            });

        $count = $query->count();
        $this->info("Found {$count} images to process.");

        $bar = $this->output->createProgressBar($count);
        $bar->start();

        foreach ($query->cursor() as $media) {
            $this->processMedia($media);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Optimization completed.');
    }

    private function processMedia(Media $media)
    {
        $disk = $media->disk ?? 'public';
        $path = $media->path;

        if (!Storage::disk($disk)->exists($path)) {
            // $this->warn("File not found: {$path}");
            return;
        }

        // Si déjà WebP et pas de force, on peut skip ou juste mettre à jour les dimensions
        // Mais pour être sûr d'avoir la compression optimale, on peut re-traiter.
        // Ici on va traiter tout ce qui est image.

        $fullPath = Storage::disk($disk)->path($path);
        
        // Optimisation logic inline (similaire au Controller mais adapté)
        $info = @getimagesize($fullPath);
        if (!$info) return;

        $mime = $info['mime'];
        $width = $info[0];
        $height = $info[1];

        // Si c'est déjà WebP et optimisé (taille < 1920), on check juste si on doit update DB
        if ($mime === 'image/webp' && max($width, $height) <= 1920 && !$this->option('force')) {
            // Update width/height if missing
            if (!$media->width || !$media->height) {
                $media->update(['width' => $width, 'height' => $height]);
            }
            return;
        }

        $create = match ($mime) {
            'image/jpeg' => 'imagecreatefromjpeg',
            'image/png' => 'imagecreatefrompng',
            'image/webp' => 'imagecreatefromwebp',
            default => null,
        };

        if (!$create || !function_exists($create)) return;

        $src = @$create($fullPath);
        if (!$src) return;

        $maxDim = 1920;
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

        // Convert to WebP
        $tmp = tempnam(sys_get_temp_dir(), 'opt_');
        imagewebp($dst, $tmp, 75); // Quality 75

        imagedestroy($src);
        imagedestroy($dst);

        // New path (same dir, extension .webp)
        $dir = pathinfo($path, PATHINFO_DIRNAME);
        $filename = pathinfo($path, PATHINFO_FILENAME);
        $newFilename = $filename . '.webp';
        $newPath = ($dir === '.' ? '' : $dir . '/') . $newFilename;

        // Save new file
        Storage::disk($disk)->put($newPath, file_get_contents($tmp));
        $newSize = filesize($tmp);
        @unlink($tmp);

        // Delete old file if name is different
        if ($path !== $newPath && Storage::disk($disk)->exists($path)) {
            Storage::disk($disk)->delete($path);
        }

        // Update DB
        $media->update([
            'path' => $newPath,
            'mime' => 'image/webp',
            'size' => $newSize,
            'width' => $targetW,
            'height' => $targetH,
        ]);
    }
}
