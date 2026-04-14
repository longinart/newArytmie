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
        $photo->loadMissing('album');

        return $this->serveScaledJpeg($photo, 'thumb-sm', 320, 52);
    }

    /**
     * Větší JPEG pro lightbox (ne plné rozlišení).
     */
    public function large(Photo $photo): Response|RedirectResponse
    {
        $photo->loadMissing('album');

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

        if ($this->isUsableJpegCacheFile($cacheFile)) {
            return response()->file($cacheFile, $this->jpegResponseHeaders($variant));
        }

        if (is_file($cacheFile)) {
            @unlink($cacheFile);
        }

        try {
            $image = ImageManager::gd()->read($absolute);
            $image->scaleDown(width: $maxEdge, height: $maxEdge);
            $encoded = (string) $image->toJpeg(quality: $jpegQuality);
        } catch (Throwable) {
            return $this->serveOriginalWhenPossible($absolute, $photo);
        }

        if (strlen($encoded) < 64) {
            return $this->serveOriginalWhenPossible($absolute, $photo);
        }

        $written = @file_put_contents($cacheFile, $encoded, LOCK_EX);
        if ($written !== strlen($encoded)) {
            @unlink($cacheFile);

            return response($encoded, 200, $this->jpegResponseHeaders($variant));
        }

        if (! $this->isUsableJpegCacheFile($cacheFile)) {
            @unlink($cacheFile);

            return response($encoded, 200, $this->jpegResponseHeaders($variant));
        }

        return response()->file($cacheFile, $this->jpegResponseHeaders($variant));
    }

    /**
     * @return array<string, string>
     */
    private function jpegResponseHeaders(string $variant): array
    {
        return [
            'Content-Type' => 'image/jpeg',
            'Cache-Control' => str_starts_with($variant, 'thumb')
                ? 'public, max-age=2592000'
                : 'public, max-age=604800',
        ];
    }

    /**
     * Poškozený nebo prázdný cache soubor — při dalším requestu znovu vygenerovat.
     */
    private function isUsableJpegCacheFile(string $cacheFile): bool
    {
        if (! is_file($cacheFile) || filesize($cacheFile) < 64) {
            return false;
        }

        $info = @getimagesize($cacheFile);

        return $info !== false && ($info[2] ?? 0) === IMAGETYPE_JPEG;
    }

    /**
     * Když GD/Intervention nezvládne typ nebo převod — pošleme originál (prohlížeč umí JPEG/PNG/WebP/GIF).
     * Lepší než redirect na /storage/… (špatné APP_URL, chování u img tagu).
     */
    private function serveOriginalWhenPossible(string $absolute, Photo $photo): Response|RedirectResponse
    {
        if (! is_readable($absolute)) {
            abort(404);
        }

        $ext = strtolower(pathinfo($absolute, PATHINFO_EXTENSION));
        $mime = match ($ext) {
            'jpg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            default => null,
        };

        if ($mime === null) {
            $detected = @mime_content_type($absolute);
            if (is_string($detected) && str_starts_with($detected, 'image/')) {
                $mime = $detected;
            }
        }

        if ($mime === null) {
            return redirect()->to(Storage::disk('public')->url($photo->image_path));
        }

        return response()->file($absolute, [
            'Content-Type' => $mime,
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }
}
