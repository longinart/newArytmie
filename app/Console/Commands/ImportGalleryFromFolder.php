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

#[Signature('app:import-gallery-from-folder
    {source : Cesta k adresari se stazenou galerii}
    {--dry-run : Jen vypis plan bez zapisu do DB a souboru}
    {--draft : Importuje alba i fotky jako koncepty}
    {--single-album= : Importuje vse do jednoho alba s timto nazvem}
')]
#[Description('Importuje fotogalerii ze slozek do Album/Photo modelu')]
class ImportGalleryFromFolder extends Command
{
    private const IMAGE_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $source = $this->normalizeSourcePath((string) $this->argument('source'));
        $dryRun = (bool) $this->option('dry-run');
        $isPublished = ! (bool) $this->option('draft');
        $singleAlbumName = trim((string) $this->option('single-album'));

        if (! File::isDirectory($source)) {
            $this->error("Adresar neexistuje: {$source}");

            return self::FAILURE;
        }

        $albumDirs = $singleAlbumName !== ''
            ? [$source]
            : $this->discoverAlbumDirectories($source);

        if ($albumDirs === []) {
            $this->warn('V zadane slozce jsem nenasel zadna alba (adresare s obrazky).');

            return self::SUCCESS;
        }

        $totalAlbums = 0;
        $totalPhotos = 0;
        $skippedPhotos = 0;

        foreach ($albumDirs as $albumDir) {
            if ($singleAlbumName !== '') {
                $albumTitle = $singleAlbumName;
                $albumSlugBase = Str::slug($singleAlbumName);
            } else {
                $relative = $this->relativePath($source, $albumDir);
                $albumTitle = $this->albumTitleFromRelativePath($relative);
                $albumSlugBase = Str::slug(str_replace('/', '-', $relative));
            }

            if ($dryRun) {
                $album = new Album([
                    'id' => 0,
                    'title' => $albumTitle,
                    'slug' => $albumSlugBase !== '' ? $albumSlugBase : Str::slug($albumTitle),
                ]);
            } else {
                $albumSlug = $this->resolveUniqueAlbumSlug(
                    $albumSlugBase !== '' ? $albumSlugBase : Str::slug($albumTitle)
                );

                $album = Album::firstOrCreate(
                    ['slug' => $albumSlug],
                    [
                        'title' => $albumTitle,
                        'description' => null,
                        'is_published' => $isPublished,
                        'published_at' => $isPublished ? now() : null,
                        'sort_order' => 0,
                    ]
                );
            }

            $totalAlbums++;
            $this->line('');
            $this->info("Album: {$albumTitle}");

            $recursiveImages = $singleAlbumName !== '';
            $imageFiles = $this->imageFilesInDirectory($albumDir, $recursiveImages);
            $imageFiles = $this->sortByPhotoDate($imageFiles);
            $sortOrder = $dryRun ? 1 : ((int) Photo::where('album_id', $album->id)->max('sort_order') + 1);

            foreach ($imageFiles as $filePath) {
                $originalName = basename($filePath);
                $cleanFileName = $this->cleanFileName($originalName);
                $photoTitle = $this->photoTitleFromFilename($originalName);
                $takenAt = $this->extractTakenAt($filePath);

                if ($dryRun) {
                    $totalPhotos++;
                    $this->line("  [DRY] {$originalName}");
                    continue;
                }

                $targetDir = 'gallery/'.$album->slug;
                $targetPath = $this->uniqueStoragePath($targetDir, $cleanFileName);

                $existing = Photo::query()
                    ->where('album_id', $album->id)
                    ->where('image_path', $targetPath)
                    ->exists();

                if ($existing) {
                    $skippedPhotos++;
                    $this->warn("  [SKIP] {$originalName} (uz existuje)");
                    continue;
                }

                Storage::disk('public')->put($targetPath, File::get($filePath));

                Photo::create([
                    'album_id' => $album->id,
                    'title' => $photoTitle,
                    'alt_text' => $photoTitle,
                    'image_path' => $targetPath,
                    'caption' => null,
                    'taken_at' => $takenAt?->format('Y-m-d'),
                    'sort_order' => $sortOrder,
                    'is_published' => $isPublished,
                ]);

                $sortOrder++;
                $totalPhotos++;
                $this->line("  [OK] {$originalName}");
            }
        }

        $this->line('');
        $this->info("Hotovo. Alba: {$totalAlbums}, fotky: {$totalPhotos}, preskoceno: {$skippedPhotos}.");
        if ($dryRun) {
            $this->comment('Bezel dry-run, nic se neulozilo.');
        }

        return self::SUCCESS;
    }

    private function normalizeSourcePath(string $source): string
    {
        if (Str::startsWith($source, ['/', '\\']) || preg_match('/^[A-Za-z]:\\\\/', $source) === 1) {
            return realpath($source) ?: $source;
        }

        $absolute = base_path($source);

        return realpath($absolute) ?: $absolute;
    }

    /**
     * @return array<int, string>
     */
    private function discoverAlbumDirectories(string $source): array
    {
        $albumDirs = [];

        $directories = File::directories($source);
        $directories[] = $source;

        while ($directories !== []) {
            $dir = array_pop($directories);
            if (! is_string($dir)) {
                continue;
            }

            if ($this->imageFilesInDirectory($dir) !== []) {
                $albumDirs[] = $dir;
                continue;
            }

            foreach (File::directories($dir) as $child) {
                $directories[] = $child;
            }
        }

        sort($albumDirs, SORT_NATURAL | SORT_FLAG_CASE);

        return $albumDirs;
    }

    /**
     * @return array<int, string>
     */
    private function imageFilesInDirectory(string $dir, bool $recursive = false): array
    {
        if (! File::isDirectory($dir)) {
            return [];
        }

        $files = [];
        $candidates = $recursive ? File::allFiles($dir) : File::files($dir);

        foreach ($candidates as $file) {
            if (! $file->isFile()) {
                continue;
            }
            $extension = strtolower($file->getExtension());
            if (! in_array($extension, self::IMAGE_EXTENSIONS, true)) {
                continue;
            }
            $real = $file->getRealPath();
            if ($real !== false) {
                $files[] = $real;
            }
        }

        sort($files, SORT_NATURAL | SORT_FLAG_CASE);

        return array_values(array_filter($files));
    }

    /**
     * @param array<int, string> $files
     * @return array<int, string>
     */
    private function sortByPhotoDate(array $files): array
    {
        usort($files, function (string $a, string $b): int {
            $aTs = $this->photoTimestamp($a);
            $bTs = $this->photoTimestamp($b);
            return $aTs <=> $bTs;
        });

        return $files;
    }

    private function photoTimestamp(string $filePath): int
    {
        $takenAt = $this->extractTakenAt($filePath);
        if ($takenAt !== null) {
            return $takenAt->getTimestamp();
        }

        $mtime = @filemtime($filePath);
        return $mtime !== false ? $mtime : PHP_INT_MAX;
    }

    private function extractTakenAt(string $filePath): ?\DateTimeImmutable
    {
        if (! function_exists('exif_read_data')) {
            return null;
        }

        $exif = @exif_read_data($filePath, 'EXIF', true);
        if (! is_array($exif)) {
            return null;
        }

        $raw = $exif['EXIF']['DateTimeOriginal'] ?? $exif['IFD0']['DateTime'] ?? null;
        if (! is_string($raw) || trim($raw) === '') {
            return null;
        }

        $raw = trim($raw);
        $parsed = \DateTimeImmutable::createFromFormat('Y:m:d H:i:s', $raw);

        return $parsed instanceof \DateTimeImmutable ? $parsed : null;
    }

    private function relativePath(string $base, string $path): string
    {
        $normalizedBase = str_replace('\\', '/', rtrim($base, '\\/'));
        $normalizedPath = str_replace('\\', '/', rtrim($path, '\\/'));

        $relative = Str::after($normalizedPath, $normalizedBase);
        $relative = trim($relative, '/');

        return $relative !== '' ? $relative : basename($normalizedPath);
    }

    private function albumTitleFromRelativePath(string $relative): string
    {
        $title = str_replace(['_', '-'], ' ', $relative);
        $title = str_replace('/', ' - ', $title);
        $title = preg_replace('/\s+/', ' ', $title) ?: $relative;

        return trim($title);
    }

    private function photoTitleFromFilename(string $filename): string
    {
        $name = pathinfo($filename, PATHINFO_FILENAME);
        $name = str_replace(['_', '-'], ' ', $name);
        $name = preg_replace('/\s+/', ' ', $name) ?: $name;

        return trim($name);
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
