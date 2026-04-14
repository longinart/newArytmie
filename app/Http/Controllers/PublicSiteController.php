<?php

namespace App\Http\Controllers;

use App\Models\Album;
use App\Models\Concert;
use App\Models\News;
use App\Models\Photo;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class PublicSiteController extends Controller
{
    public function home()
    {
        $newsItems = collect();
        if (Schema::hasTable('news')) {
            $newsItems = News::query()
                ->where('is_published', true)
                ->whereNotNull('published_at')
                ->where('published_at', '<=', now())
                ->latest('published_at')
                ->limit(3)
                ->get();
        }

        $concertItems = collect();
        if (Schema::hasTable('concerts')) {
            $concertItems = Concert::query()
                ->where('is_published', true)
                ->where('starts_at', '>=', now()->subDay())
                ->orderBy('starts_at')
                ->limit(3)
                ->get();
        }

        $galleryPreviewPhotos = collect();
        if (Schema::hasTable('photos') && Schema::hasTable('albums')) {
            $galleryPreviewPhotos = Photo::query()
                ->where('is_published', true)
                ->whereHas('album', fn ($q) => $q->where('is_published', true))
                ->inRandomOrder()
                ->limit(4)
                ->with('album')
                ->get();
        }

        return view('welcome', compact('newsItems', 'concertItems', 'galleryPreviewPhotos'));
    }

    public function about()
    {
        return view('about');
    }

    public function showNews(string $slug)
    {
        abort_unless(Schema::hasTable('news'), 404);

        $news = News::query()
            ->where('slug', $slug)
            ->where('is_published', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->firstOrFail();

        return view('news.show', compact('news'));
    }

    public function showConcert(string $slug)
    {
        abort_unless(Schema::hasTable('concerts'), 404);

        $concert = Concert::query()
            ->where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        return view('concerts.show', compact('concert'));
    }

    public function gallery(Request $request)
    {
        $albums = collect();

        if (Schema::hasTable('albums')) {
            $albums = Album::query()
                ->where('is_published', true)
                ->withCount(['photos as published_photos_count' => fn ($q) => $q->where('is_published', true)])
                ->withMax(['photos as latest_published_taken_at' => fn ($q) => $q->where('is_published', true)], 'taken_at')
                ->with(['photos' => fn ($query) => $query->where('is_published', true)->orderBy('sort_order')->limit(1)])
                ->orderByDesc('published_at')
                ->orderBy('title')
                ->get();
        }
        $filter = $request->string('typ')->toString();
        $filteredAlbums = $albums->filter(function (Album $album) use ($filter): bool {
            if ($filter === '' || $filter === 'vse') {
                return true;
            }

            $raw = $album->latest_published_taken_at;
            $latestTakenAt = $raw !== null ? Carbon::parse($raw)->startOfDay() : null;
            if (! $latestTakenAt) {
                return $filter !== 'archiv';
            }

            $isArchive = $latestTakenAt->lt(now()->subYears(2));

            return $filter === 'archiv' ? $isArchive : ! $isArchive;
        })->values();

        return view('gallery.index', [
            'albums' => $filteredAlbums,
            'activeFilter' => in_array($filter, ['vse', 'nove', 'archiv'], true) ? $filter : 'vse',
        ]);
    }

    public function showAlbum(string $slug)
    {
        abort_unless(Schema::hasTable('albums'), 404);

        $album = Album::query()
            ->where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        // 12 fotek na stránku (mřížka 4×3) — méně paralelních náhledů, rychlejší načtení.
        $perPage = 12;

        $photoPaginator = $album->photos()
            ->where('is_published', true)
            ->orderBy('sort_order')
            ->paginate($perPage)
            ->withQueryString();

        $photosByYear = $photoPaginator->getCollection()
            ->groupBy(fn ($photo) => $photo->taken_at?->format('Y') ?? 'Nezařazeno')
            ->sortKeysDesc();

        // Stejné pořadí jako v šabloně (rok sestupně → fotky v rámci roku), jinak index v mřížce nesedí s lightboxem.
        $lightboxPhotos = [];
        foreach ($photosByYear as $photosInYear) {
            foreach ($photosInYear as $photo) {
                $lightboxPhotos[] = [
                    'large' => route('gallery.photo.large', $photo),
                    'full' => Storage::disk('public')->url($photo->image_path),
                    'thumb' => route('gallery.photo.thumb', $photo),
                    'alt' => $photo->alt_text ?: ($photo->title ?: $album->title),
                    'caption' => $photo->caption ?? '',
                ];
            }
        }

        return view('gallery.show', compact('album', 'photosByYear', 'photoPaginator', 'lightboxPhotos'));
    }
}
