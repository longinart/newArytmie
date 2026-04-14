<?php

namespace App\Http\Controllers;

use App\Models\Photo;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Throwable;

class GalleryPhotoController extends Controller
{
    /**
     * Veřejný náhled fotky (menší JPEG) — používá se v mřížce galerie a na úvodu.
     */
    public function thumbnail(Photo $photo): Response
    {
        abort_unless($photo->is_published, 404);
        abort_unless($photo->album && $photo->album->is_published, 404);

        $absolute = Storage::disk('public')->path($photo->image_path);
        abort_unless(is_file($absolute), 404);

        try {
            $image = ImageManager::gd()->read($absolute);
            // Náhledy v mřížce: menší soubor, rychlejší načítání stránky.
            $image->scaleDown(width: 480, height: 480);
            $encoded = $image->toJpeg(quality: 68);
        } catch (Throwable) {
            // Např. chybějící rozšíření GD na serveru — vrátíme originál místo prázdného náhledu.
            return redirect(Storage::disk('public')->url($photo->image_path));
        }

        return response((string) $encoded, 200, [
            'Content-Type' => 'image/jpeg',
            'Cache-Control' => 'public, max-age=2592000',
        ]);
    }

    /**
     * Větší JPEG pro lightbox (ne plné rozlišení) — rychlejší než originál z disku.
     */
    public function large(Photo $photo): Response
    {
        abort_unless($photo->is_published, 404);
        abort_unless($photo->album && $photo->album->is_published, 404);

        $absolute = Storage::disk('public')->path($photo->image_path);
        abort_unless(is_file($absolute), 404);

        try {
            $image = ImageManager::gd()->read($absolute);
            $image->scaleDown(width: 1400, height: 1400);
            $encoded = $image->toJpeg(quality: 78);
        } catch (Throwable) {
            return redirect(Storage::disk('public')->url($photo->image_path));
        }

        return response((string) $encoded, 200, [
            'Content-Type' => 'image/jpeg',
            'Cache-Control' => 'public, max-age=604800',
        ]);
    }
}
