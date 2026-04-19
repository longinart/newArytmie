<!DOCTYPE html>
<html lang="cs">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $concert->seo_title ?: $concert->title }} | Arytmie Praha</title>
        <meta name="description" content="{{ $concert->seo_description ?: \Illuminate\Support\Str::limit(strip_tags($concert->description ?: $concert->program ?: $concert->title), 155) }}">
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
    </head>
    <body class="bg-slate-800 text-slate-100 antialiased">
        @php
            $coverLightbox = [];
            if ($concert->cover_image_path) {
                $coverUrl = \Illuminate\Support\Facades\Storage::disk('public')->url($concert->cover_image_path);
                $coverLightbox = [
                    [
                        'large' => $coverUrl,
                        'full' => $coverUrl,
                        'alt' => $concert->title,
                        'caption' => '',
                    ],
                ];
            }
        @endphp

        <main class="mx-auto max-w-4xl px-4 py-10 sm:px-6 lg:px-8">
            <a href="{{ route('home') }}" class="text-sm text-orange-500 hover:text-orange-600">&larr; Zpět na úvod</a>

            @if ($concert->cover_image_path)
                <div
                    class="mt-6"
                    x-data="homeGalleryPeek(@js($coverLightbox))"
                    @keydown.window="handleKey($event)"
                >
                    <article class="rounded-3xl border border-orange-100 bg-white p-6 sm:p-8">
                        @include('concerts.partials.concert-article-inner', ['showCover' => true])
                    </article>

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
                                    class="absolute right-2 top-2 z-[102] rounded-full border border-white/20 bg-slate-900/90 px-3 py-1.5 text-sm font-semibold text-white shadow transition hover:bg-slate-800 sm:right-4 sm:top-4"
                                    @click.stop="close()"
                                >
                                    Zavřít (Esc)
                                </button>

                                <div class="relative z-[101] flex max-h-[90vh] max-w-[min(100vw-2rem,1200px)] flex-col items-center justify-center">
                                    <img
                                        x-bind:key="'concert-lb-' + i"
                                        :src="peekSrc()"
                                        :alt="peekAlt()"
                                        class="max-h-[min(78vh,900px)] w-auto max-w-full rounded-lg object-contain shadow-2xl"
                                        loading="eager"
                                        decoding="async"
                                        referrerpolicy="no-referrer-when-downgrade"
                                        @click.stop
                                    >
                                    <p
                                        x-show="peekCaption() !== ''"
                                        x-text="peekCaption()"
                                        class="mt-4 max-w-2xl text-center text-sm leading-relaxed text-slate-200"
                                    ></p>
                                </div>
                            </div>
                        </template>
                    </template>
                </div>
            @else
                <article class="mt-6 rounded-3xl border border-orange-100 bg-white p-6 sm:p-8">
                    @include('concerts.partials.concert-article-inner', ['showCover' => false])
                </article>
            @endif
        </main>
    </body>
</html>
