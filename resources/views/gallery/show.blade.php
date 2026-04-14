<!DOCTYPE html>
<html lang="cs">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $album->title }} | Fotogalerie Arytmie Praha</title>
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
    </head>
    <body class="bg-slate-800 text-slate-100 antialiased">
        <div
            class="mx-auto max-w-6xl px-4 py-10 sm:px-6 lg:px-8"
            x-data="albumGallery(@js($lightboxPhotos))"
            @keydown.window="handleKey($event)"
        >
            <a href="{{ route('gallery.index') }}" class="text-sm font-medium text-orange-400 transition hover:text-orange-300">&larr; Zpět na galerii</a>
            <h1 class="mt-4 text-3xl font-semibold text-white">{{ $album->title }}</h1>
            @if ($album->description)
                <p class="mt-2 max-w-3xl text-slate-300">{{ $album->description }}</p>
            @endif

            @if ($photoPaginator->total() > 0)
                <p class="mt-4 text-sm text-slate-400">
                    Zobrazeno {{ $photoPaginator->firstItem() }}–{{ $photoPaginator->lastItem() }} z {{ $photoPaginator->total() }} fotek
                    <span class="text-slate-500"> — kliknutím otevřete velký náhled, šipky na klávesnici pro další fotku.</span>
                </p>
            @endif

            @php $gridPhotoIndex = 0; @endphp
            @forelse ($photosByYear as $year => $photos)
                <section class="mt-8">
                    <h2 class="mb-4 text-xl font-semibold text-white">{{ $year }}</h2>
                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ($photos as $photo)
                            @php $idx = $gridPhotoIndex++; @endphp
                            <figure class="overflow-hidden rounded-xl border border-slate-600/80 bg-slate-900/50 shadow-sm">
                                <button
                                    type="button"
                                    class="block w-full cursor-zoom-in text-left focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-400"
                                    @click="openAt({{ $idx }})"
                                >
                                    <img
                                        src="{{ route('gallery.photo.thumb', $photo) }}"
                                        alt="{{ $photo->alt_text ?: $photo->title ?: $album->title }}"
                                        class="aspect-[4/3] w-full object-cover"
                                        data-retryable-image
                                        loading="{{ $idx === 0 ? 'eager' : 'lazy' }}"
                                        decoding="async"
                                        @if ($idx === 0)
                                            fetchpriority="high"
                                        @elseif ($idx >= 10)
                                            fetchpriority="low"
                                        @endif
                                        width="480"
                                        height="360"
                                    >
                                </button>
                                @if ($photo->caption)
                                    <figcaption class="px-3 py-2 text-sm text-slate-300">
                                        {{ $photo->caption }}
                                    </figcaption>
                                @endif
                            </figure>
                        @endforeach
                    </div>
                </section>
            @empty
                <p class="mt-8 text-sm text-slate-400">Album zatím neobsahuje žádné publikované fotky.</p>
            @endforelse

            @if ($photoPaginator->hasPages())
                <nav class="mt-10 flex justify-center" aria-label="Stránkování fotek">
                    {{ $photoPaginator->onEachSide(1)->links() }}
                </nav>
            @endif

            <template x-teleport="body">
                <template x-if="open">
                    <div
                        class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-8"
                        x-transition
                        role="dialog"
                        aria-modal="true"
                        aria-label="Zvětšená fotografie"
                    >
                        <div class="absolute inset-0 bg-black/90" @click="close()" aria-hidden="true"></div>

                        <button
                            type="button"
                            class="absolute left-2 top-1/2 z-[102] -translate-y-1/2 rounded-full border border-white/20 bg-slate-900/80 px-3 py-4 text-2xl text-white shadow-lg transition hover:bg-slate-800 sm:left-4"
                            @click.stop="prev()"
                            aria-label="Předchozí fotografie"
                        >
                            ‹
                        </button>
                        <button
                            type="button"
                            class="absolute right-2 top-1/2 z-[102] -translate-y-1/2 rounded-full border border-white/20 bg-slate-900/80 px-3 py-4 text-2xl text-white shadow-lg transition hover:bg-slate-800 sm:right-4"
                            @click.stop="next()"
                            aria-label="Další fotografie"
                        >
                            ›
                        </button>

                        <button
                            type="button"
                            class="absolute right-2 top-2 z-[102] rounded-full border border-white/20 bg-slate-900/90 px-3 py-1.5 text-sm font-semibold text-white shadow transition hover:bg-slate-800 sm:right-4 sm:top-4"
                            @click.stop="close()"
                        >
                            Zavřít (Esc)
                        </button>

                        <div class="relative z-[101] flex max-h-[90vh] max-w-[min(100vw-2rem,1200px)] flex-col items-center justify-center">
                            <img
                                x-bind:key="'lb-' + i"
                                :src="lightboxSrc()"
                                :alt="lightboxAlt()"
                                class="max-h-[min(78vh,900px)] w-auto max-w-full rounded-lg object-contain shadow-2xl"
                                loading="eager"
                                decoding="async"
                                referrerpolicy="no-referrer-when-downgrade"
                                @click.stop
                            >
                            <p
                                x-show="photos[i] && photos[i].caption"
                                x-text="photos[i] ? photos[i].caption : ''"
                                class="mt-4 max-w-2xl text-center text-sm leading-relaxed text-slate-200"
                            ></p>
                        </div>
                    </div>
                </template>
            </template>
        </div>
        <script>
            (() => {
                const maxRetries = 2;
                const baseDelayMs = 600;

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
