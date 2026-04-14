<?php

namespace App\Http\Controllers;

use App\Models\Photo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Throwable;

class GalleryPhotoController extends Controller
{
    /**
     * Veřejný náhled fotky (menší JPEG) — používá se v mřížce galerie a na úvodu.
     */
    public function thumbnail(Photo $photo): Response|RedirectResponse
    {
        return $this->serveScaledJpeg($photo, 'thumb-sm', 320, 52);
    }

    /**
     * Větší JPEG pro lightbox (ne plné rozlišení).
     */
    public function large(Photo $photo): Response|RedirectResponse
    {
        return $this->serveScaledJpeg($photo, 'large-md', 960, 70);
    }

    private function serveScaledJpeg(Photo $photo, string $variant, int $maxEdge, int $jpegQuality): Response|RedirectResponse
    {
        abort_unless($photo->is_published, 404);
        abort_unless($photo->album && $photo->album->is_published, 404);

        $absolute = Storage::disk('public')->path($photo->image_path);
        abort_unless(is_file($absolute), 404);

        $mtime = filemtime($absolute) ?: 0;
        $cacheDir = storage_path('app/gallery-cache');
        if (! is_dir($cacheDir)) {
            mkdir($cacheDir, 0755, true);
        }

        $cacheFile = $cacheDir.DIRECTORY_SEPARATOR.hash('sha256', $variant.'|'.$mtime.'|'.$absolute.'|'.$photo->id).'.jpg';

        if (is_file($cacheFile) && filesize($cacheFile) > 0) {
            return response()->file($cacheFile, [
                'Content-Type' => 'image/jpeg',
                'Cache-Control' => str_starts_with($variant, 'thumb')
                    ? 'public, max-age=2592000'
                    : 'public, max-age=604800',
            ]);
        }

        try {
            $image = ImageManager::gd()->read($absolute);
            $image->scaleDown(width: $maxEdge, height: $maxEdge);
            $encoded = (string) $image->toJpeg(quality: $jpegQuality);
        } catch (Throwable) {
            return redirect(Storage::disk('public')->url($photo->image_path));
        }

        file_put_contents($cacheFile, $encoded, LOCK_EX);

        return response($encoded, 200, [
            'Content-Type' => 'image/jpeg',
            'Cache-Control' => str_starts_with($variant, 'thumb')
                ? 'public, max-age=2592000'
                : 'public, max-age=604800',
        ]);
    }
}
