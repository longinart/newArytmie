<?php

namespace App\Console\Commands;

use App\Models\Album;
use App\Models\Photo;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

#[Signature('app:merge-gallery-folders
    {from : Slug zdrojoveho alba (slozka storage/app/public/gallery/{slug})}
    {into : Slug ciloveho alba}
    {--dry-run : Jen vypsat zmeny bez zapisu}
')]
#[Description('Slouci galerijni slozku do jineho alba (presun souboru, DB, smazani prazdneho alba)')]
class MergeGalleryFolders extends Command
{
    private const IMAGE_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

    public function handle(): int
    {
        $fromSlug = (string) $this->argument('from');
        $intoSlug = (string) $this->argument('into');
        $dryRun = (bool) $this->option('dry-run');

        $intoAlbum = Album::query()->where('slug', $intoSlug)->first();
        if (! $intoAlbum) {
            $this->error("Cilove album se slugem \"{$intoSlug}\" neexistuje.");

            return self::FAILURE;
        }

        $fromAlbum = Album::query()->where('slug', $fromSlug)->first();
        $disk = Storage::disk('public');
        $fromPrefix = 'gallery/'.$fromSlug;

        if ($fromSlug === $intoSlug) {
            $this->error('Zdroj a cil musi byt ruzne slugy.');

            return self::FAILURE;
        }

        $this->info("Slouceni \"{$fromSlug}\" -> \"{$intoSlug}\" (dry-run: ".($dryRun ? 'ano' : 'ne').')');

        if (! $disk->exists($fromPrefix)) {
            $this->warn("Slozka \"{$fromPrefix}\" na disku neexistuje.");
        }

        $photos = Photo::query()
            ->where(function ($q) use ($fromAlbum, $fromPrefix) {
                $q->where('image_path', 'like', $fromPrefix.'/%');
                if ($fromAlbum) {
                    $q->orWhere('album_id', $fromAlbum->id);
                }
            })
            ->orderBy('id')
            ->get();

        $this->line('Zaznamy ve foto tabulce: '.$photos->count());

        $nextSort = ((int) Photo::query()->where('album_id', $intoAlbum->id)->max('sort_order')) + 1;

        foreach ($photos as $photo) {
            $oldPath = $photo->image_path;
            if ($oldPath === '' || $oldPath === null) {
                $this->warn("Preskakuji foto id={$photo->id} – prazdna cesta.");

                continue;
            }

            if (! $disk->exists($oldPath)) {
                $this->warn("Preskakuji foto id={$photo->id} – soubor neexistuje: {$oldPath}");

                continue;
            }

            $fileName = basename($oldPath);
            $newPath = $this->uniqueTargetPath($disk, $intoSlug, $fileName);

            if ($dryRun) {
                $this->line("[DRY] foto id={$photo->id}: {$oldPath} -> {$newPath}, album_id -> {$intoAlbum->id}");

                continue;
            }

            DB::transaction(function () use ($disk, $photo, $intoAlbum, $oldPath, $newPath, &$nextSort): void {
                if ($oldPath !== $newPath) {
                    $disk->move($oldPath, $newPath);
                }
                $photo->album_id = $intoAlbum->id;
                $photo->image_path = $newPath;
                $photo->sort_order = $nextSort++;
                $photo->save();
            });

            $this->line("OK foto id={$photo->id} -> {$newPath}");
        }

        $importedOrphans = 0;
        if ($disk->exists($fromPrefix)) {
            foreach ($disk->allFiles($fromPrefix) as $relativePath) {
                $ext = strtolower((string) pathinfo($relativePath, PATHINFO_EXTENSION));
                if (! in_array($ext, self::IMAGE_EXTENSIONS, true)) {
                    continue;
                }

                if (Photo::query()->where('image_path', $relativePath)->exists()) {
                    continue;
                }

                $fileName = basename($relativePath);
                $newPath = $this->uniqueTargetPath($disk, $intoSlug, $fileName);

                if ($dryRun) {
                    $this->line("[DRY] orphan soubor: {$relativePath} -> {$newPath} (+ novy Photo)");

                    continue;
                }

                DB::transaction(function () use ($disk, $intoAlbum, $relativePath, $newPath, $fileName, &$nextSort, &$importedOrphans): void {
                    $disk->move($relativePath, $newPath);
                    $title = $this->titleFromFilename($fileName);
                    Photo::create([
                        'album_id' => $intoAlbum->id,
                        'title' => $title,
                        'alt_text' => $title,
                        'image_path' => $newPath,
                        'caption' => null,
                        'taken_at' => null,
                        'sort_order' => $nextSort++,
                        'is_published' => true,
                    ]);
                    $importedOrphans++;
                });
            }
        }

        if (! $dryRun && $disk->exists($fromPrefix)) {
            $disk->deleteDirectory($fromPrefix);
            $this->info("Smazana slozka: {$fromPrefix}");
        }

        if (! $dryRun && $fromAlbum) {
            $left = Photo::query()->where('album_id', $fromAlbum->id)->count();
            if ($left === 0) {
                $fromAlbum->delete();
                $this->info("Smazano prazdne album: {$fromSlug}");
            } else {
                $this->warn("Album \"{$fromSlug}\" stale obsahuje {$left} fotek v DB – nemaazu zaznam alba.");
            }
        }

        if (! $dryRun) {
            $intoAlbum->refresh();
            $cover = (string) ($intoAlbum->cover_image_path ?? '');
            if ($cover !== '' && str_starts_with($cover, $fromPrefix.'/')) {
                $intoAlbum->forceFill(['cover_image_path' => null])->save();
                $this->comment('cover_image_path na cilovem albu byl v rozcesti zdrojove slozky – vynulovan (pouzije se prvni fotka).');
            }
        }

        if ($importedOrphans > 0) {
            $this->info("Pridano fotek ze souboru bez DB: {$importedOrphans}");
        }

        $this->info('Hotovo.');

        return self::SUCCESS;
    }

    private function uniqueTargetPath(Filesystem $disk, string $intoSlug, string $filename): string
    {
        $dir = 'gallery/'.$intoSlug;
        $path = "{$dir}/{$filename}";
        $counter = 1;

        while ($disk->exists($path)) {
            $name = pathinfo($filename, PATHINFO_FILENAME);
            $ext = pathinfo($filename, PATHINFO_EXTENSION);
            $path = "{$dir}/{$name}-{$counter}.{$ext}";
            $counter++;
        }

        return $path;
    }

    private function titleFromFilename(string $filename): string
    {
        $name = pathinfo($filename, PATHINFO_FILENAME);
        $name = str_replace(['_', '-'], ' ', $name);
        $name = preg_replace('/\s+/', ' ', $name) ?: $name;

        return trim($name);
    }
}
