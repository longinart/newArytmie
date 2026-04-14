<?php

namespace App\Http\Controllers;

use App\Models\Concert;
use App\Models\News;
use App\Models\Album;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

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

        return view('welcome', compact('newsItems', 'concertItems'));
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
                ->with(['photos' => fn ($query) => $query->where('is_published', true)->orderBy('sort_order')])
                ->orderByDesc('published_at')
                ->orderBy('title')
                ->get();
        }
        $filter = $request->string('typ')->toString();
        $filteredAlbums = $albums->filter(function (Album $album) use ($filter): bool {
            if ($filter === '' || $filter === 'vse') {
                return true;
            }

            $latestTakenAt = $album->photos->max('taken_at');
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
            ->with(['photos' => fn ($query) => $query->where('is_published', true)->orderBy('sort_order')])
            ->firstOrFail();

        $photosByYear = $album->photos
            ->groupBy(fn ($photo) => $photo->taken_at?->format('Y') ?? 'Nezařazeno')
            ->sortKeysDesc();

        return view('gallery.show', compact('album', 'photosByYear'));
    }
}
