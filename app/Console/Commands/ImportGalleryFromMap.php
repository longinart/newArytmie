<?php

namespace App\Console\Commands;

use App\Models\Album;
use App\Models\Photo;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:import-gallery-from-map
    {map : Markdown/txt soubor s albumy a nazvy fotek}
    {flat_source : Slozka, kde jsou vsechny fotky pohromade}
    {--dry-run : Jen vypis plan bez zapisu}
    {--draft : Importuje jako koncepty}
')]
#[Description('Import galerie z mapy alb a jedne ploche slozky fotek')]
class ImportGalleryFromMap extends Command
{
    private const IMAGE_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $mapPath = $this->resolvePath((string) $this->argument('map'));
        $flatSource = $this->resolvePath((string) $this->argument('flat_source'));
        $dryRun = (bool) $this->option('dry-run');
        $isPublished = ! (bool) $this->option('draft');

        if (! File::exists($mapPath)) {
            $this->error("Map soubor neexistuje: {$mapPath}");
            return self::FAILURE;
        }

        if (! File::isDirectory($flatSource)) {
            $this->error("Slozka s fotkami neexistuje: {$flatSource}");
            return self::FAILURE;
        }

        $map = $this->parseMapFile(File::get($mapPath));
        if ($map === []) {
            $this->error('V map souboru jsem nenasel zadna alba s obrazky.');
            $this->comment('Mapa musi obsahovat nadpisy "### Nazev alba" a pod nimi nazvy souboru (napr. IMG_0001.jpg).');
            return self::FAILURE;
        }

        $flatFiles = $this->buildFlatFileIndex($flatSource);
        if ($flatFiles === []) {
            $this->error('Ve slozce s fotkami nejsou zadne podporovane obrazky.');
            return self::FAILURE;
        }

        $importedAlbums = 0;
        $importedPhotos = 0;
        $missingPhotos = 0;

        foreach ($map as $albumTitle => $photoNames) {
            $albumSlug = Str::slug($albumTitle);
            if ($albumSlug === '') {
                $albumSlug = 'album';
            }

            $album = null;
            if (! $dryRun) {
                $album = Album::firstOrCreate(
                    ['slug' => $this->resolveUniqueAlbumSlug($albumSlug)],
                    [
                        'title' => $albumTitle,
                        'description' => null,
                        'is_published' => $isPublished,
                        'published_at' => $isPublished ? now() : null,
                        'sort_order' => 0,
                    ]
                );
            }

            $this->line('');
            $this->info("Album: {$albumTitle}");
            $importedAlbums++;
            $sortOrder = 1;

            foreach ($photoNames as $photoName) {
                $matchedPath = $this->findFileInFlatIndex($photoName, $flatFiles);

                if ($matchedPath === null) {
                    $missingPhotos++;
                    $this->warn("  [MISS] {$photoName}");
                    continue;
                }

                if ($dryRun) {
                    $importedPhotos++;
                    $this->line("  [DRY] {$photoName}");
                    continue;
                }

                $cleanFileName = $this->cleanFileName(basename($matchedPath));
                $targetDir = 'gallery/'.$album->slug;
                $targetPath = $this->uniqueStoragePath($targetDir, $cleanFileName);

                Storage::disk('public')->put($targetPath, File::get($matchedPath));

                Photo::create([
                    'album_id' => $album->id,
                    'title' => $this->photoTitleFromFilename(basename($matchedPath)),
                    'alt_text' => $this->photoTitleFromFilename(basename($matchedPath)),
                    'image_path' => $targetPath,
                    'caption' => null,
                    'taken_at' => null,
                    'sort_order' => $sortOrder,
                    'is_published' => $isPublished,
                ]);

                $sortOrder++;
                $importedPhotos++;
                $this->line("  [OK] {$photoName}");
            }
        }

        $this->line('');
        $this->info("Hotovo. Alba: {$importedAlbums}, fotky import: {$importedPhotos}, nenalezeno: {$missingPhotos}.");
        if ($dryRun) {
            $this->comment('Bezel dry-run, nic se neulozilo.');
        }

        return self::SUCCESS;
    }

    private function resolvePath(string $path): string
    {
        if (preg_match('/^[A-Za-z]:\\\\/', $path) === 1 || Str::startsWith($path, ['/', '\\'])) {
            return realpath($path) ?: $path;
        }

        $absolute = base_path($path);
        return realpath($absolute) ?: $absolute;
    }

    /**
     * @return array<string, array<int, string>>
     */
    private function parseMapFile(string $content): array
    {
        $lines = preg_split('/\R/u', $content) ?: [];
        $albums = [];
        $currentAlbum = null;

        foreach ($lines as $line) {
            $trimmed = trim($line);
            if ($trimmed === '') {
                continue;
            }

            if (Str::startsWith($trimmed, '### ')) {
                $currentAlbum = trim(Str::after($trimmed, '### '));
                if ($currentAlbum !== '') {
                    $albums[$currentAlbum] = $albums[$currentAlbum] ?? [];
                }
                continue;
            }

            if ($currentAlbum === null) {
                continue;
            }

            if ($this->looksLikeImageFilename($trimmed)) {
                $albums[$currentAlbum][] = $trimmed;
            }
        }

        return array_filter($albums, fn ($items) => $items !== []);
    }

    private function looksLikeImageFilename(string $value): bool
    {
        $value = trim($value, "[]() \t\n\r\0\x0B");
        $extension = strtolower(pathinfo($value, PATHINFO_EXTENSION));
        return in_array($extension, self::IMAGE_EXTENSIONS, true);
    }

    /**
     * @return array<string, array<int, string>>
     */
    private function buildFlatFileIndex(string $flatSource): array
    {
        $index = [];
        foreach (File::files($flatSource) as $file) {
            $ext = strtolower($file->getExtension());
            if (! in_array($ext, self::IMAGE_EXTENSIONS, true)) {
                continue;
            }

            $fullPath = $file->getRealPath();
            if (! $fullPath) {
                continue;
            }

            $keys = [
                $this->normalizeFileKey($file->getFilename()),
                $this->normalizeFileKey(pathinfo($file->getFilename(), PATHINFO_FILENAME)),
            ];

            foreach ($keys as $key) {
                $index[$key] = $index[$key] ?? [];
                $index[$key][] = $fullPath;
            }
        }

        return $index;
    }

    /**
     * @param array<string, array<int, string>> $index
     */
    private function findFileInFlatIndex(string $requestedName, array &$index): ?string
    {
        $keys = [
            $this->normalizeFileKey($requestedName),
            $this->normalizeFileKey(pathinfo($requestedName, PATHINFO_FILENAME)),
        ];

        foreach ($keys as $key) {
            if (isset($index[$key]) && $index[$key] !== []) {
                return array_shift($index[$key]);
            }
        }

        return null;
    }

    private function normalizeFileKey(string $value): string
    {
        $value = Str::lower(trim($value));
        $value = str_replace(['_', '-', '(', ')'], ' ', $value);
        $value = preg_replace('/\s+/', ' ', $value) ?: $value;
        return trim($value);
    }

    private function cleanFileName(string $filename): string
    {
        $name = pathinfo($filename, PATHINFO_FILENAME);
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $slug = Str::slug($name);
        if ($slug === '') {
            $slug = 'photo';
        }
        return "{$slug}.{$ext}";
    }

    private function uniqueStoragePath(string $dir, string $filename): string
    {
        $path = "{$dir}/{$filename}";
        $counter = 1;

        while (Storage::disk('public')->exists($path)) {
            $name = pathinfo($filename, PATHINFO_FILENAME);
            $ext = pathinfo($filename, PATHINFO_EXTENSION);
            $path = "{$dir}/{$name}-{$counter}.{$ext}";
            $counter++;
        }

        return $path;
    }

    private function photoTitleFromFilename(string $filename): string
    {
        $name = pathinfo($filename, PATHINFO_FILENAME);
        $name = str_replace(['_', '-'], ' ', $name);
        $name = preg_replace('/\s+/', ' ', $name) ?: $name;

        return trim($name);
    }

    private function resolveUniqueAlbumSlug(string $baseSlug): string
    {
        $slug = $baseSlug !== '' ? $baseSlug : 'album';
        $candidate = $slug;
        $counter = 1;

        while (Album::where('slug', $candidate)->exists()) {
            $candidate = "{$slug}-{$counter}";
            $counter++;
        }

        return $candidate;
    }
}
