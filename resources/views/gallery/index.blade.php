<!DOCTYPE html>
<html lang="cs">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Fotogalerie | Arytmie Praha</title>
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
    </head>
    <body class="bg-stone-50 text-stone-900 antialiased">
        <main class="mx-auto max-w-6xl px-4 py-10 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <h1 class="text-3xl font-semibold">Fotogalerie</h1>
                <a href="{{ route('home') }}" class="text-sm text-teal-700 hover:text-teal-800">&larr; Zpět na úvod</a>
            </div>

            <div class="mt-6 flex flex-wrap gap-2">
                <a href="{{ route('gallery.index', ['typ' => 'vse']) }}" class="rounded-full px-4 py-2 text-sm {{ $activeFilter === 'vse' ? 'bg-teal-700 text-white' : 'bg-white text-stone-700 border border-stone-200' }}">
                    Všechna alba
                </a>
                <a href="{{ route('gallery.index', ['typ' => 'nove']) }}" class="rounded-full px-4 py-2 text-sm {{ $activeFilter === 'nove' ? 'bg-teal-700 text-white' : 'bg-white text-stone-700 border border-stone-200' }}">
                    Nové fotky
                </a>
                <a href="{{ route('gallery.index', ['typ' => 'archiv']) }}" class="rounded-full px-4 py-2 text-sm {{ $activeFilter === 'archiv' ? 'bg-teal-700 text-white' : 'bg-white text-stone-700 border border-stone-200' }}">
                    Archiv
                </a>
            </div>

            <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @forelse ($albums as $album)
                    @php $albumCardIndex = $loop->index; @endphp
                    <a href="{{ route('gallery.show', $album->slug) }}" class="rounded-2xl border border-teal-100 bg-white p-4 shadow-sm hover:shadow">
                        @php $cover = $album->cover_image_path ?: $album->photos->first()?->image_path; @endphp
                        @if ($cover)
                            <img
                                src="{{ Storage::disk('public')->url($cover) }}"
                                alt="{{ $album->title }}"
                                class="h-48 w-full rounded-xl object-cover"
                                data-retryable-image
                                loading="{{ $albumCardIndex < 6 ? 'eager' : 'lazy' }}"
                                decoding="{{ $albumCardIndex < 6 ? 'sync' : 'async' }}"
                                @if ($albumCardIndex < 3)
                                    fetchpriority="high"
                                @elseif ($albumCardIndex >= 6)
                                    fetchpriority="low"
                                @endif
                            >
                        @else
                            <div class="h-48 w-full rounded-xl bg-teal-100"></div>
                        @endif
                        <h2 class="mt-4 text-lg font-semibold">{{ $album->title }}</h2>
                        <p class="mt-1 text-sm text-stone-600">
                            {{ $album->published_photos_count }}
                            @if ($album->published_photos_count === 1)
                                fotka
                            @elseif ($album->published_photos_count >= 2 && $album->published_photos_count <= 4)
                                fotky
                            @else
                                fotek
                            @endif
                        </p>
                        @if ($album->description)
                            <p class="mt-2 text-sm text-stone-700">{{ \Illuminate\Support\Str::limit($album->description, 120) }}</p>
                        @endif
                    </a>
                @empty
                    <p class="text-sm text-stone-600">Galerie je zatím prázdná.</p>
                @endforelse
            </div>
        </main>
        <script>
            (() => {
                const maxRetries = 2;
                const baseDelayMs = 300;

                document.querySelectorAll('img[data-retryable-image]').forEach((img) => {
                    const originalSrc = img.currentSrc || img.getAttribute('src');
                    if (!originalSrc) return;

                    img.dataset.retryCount = '0';

                    img.addEventListener('error', () => {
                        const currentRetries = Number(img.dataset.retryCount || '0');
                        if (currentRetries >= maxRetries) {
                            return;
                        }

                        const nextRetry = currentRetries + 1;
                        img.dataset.retryCount = String(nextRetry);

                        const retryUrl = new URL(originalSrc, window.location.origin);
                        retryUrl.searchParams.set('_img_retry', String(nextRetry));

                        setTimeout(() => {
                            img.src = retryUrl.toString();
                        }, baseDelayMs * nextRetry);
                    });
                });
            })();
        </script>
    </body>
</html>
