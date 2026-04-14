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
    <body class="bg-stone-50 text-stone-900 antialiased">
        <main class="mx-auto max-w-4xl px-4 py-10 sm:px-6 lg:px-8">
            <a href="{{ route('home') }}" class="text-sm text-teal-700 hover:text-teal-800">&larr; Zpět na úvod</a>

            <article class="mt-6 rounded-3xl border border-teal-100 bg-white p-6 sm:p-8">
                <p class="text-sm text-teal-700">
                    {{ $concert->starts_at?->format('d.m.Y H:i') }}
                    @if($concert->ends_at)
                        - {{ $concert->ends_at->format('d.m.Y H:i') }}
                    @endif
                </p>
                <h1 class="mt-2 text-3xl font-semibold tracking-tight sm:text-4xl">{{ $concert->title }}</h1>
                <p class="mt-3 text-stone-700">
                    {{ $concert->venue_name }}, {{ $concert->city }}
                    @if($concert->venue_address)
                        - {{ $concert->venue_address }}
                    @endif
                </p>

                @if($concert->program)
                    <section class="mt-8">
                        <h2 class="text-lg font-semibold">Program</h2>
                        <p class="mt-2 whitespace-pre-line text-stone-700">{{ $concert->program }}</p>
                    </section>
                @endif

                @if($concert->description)
                    <section class="mt-8">
                        <h2 class="text-lg font-semibold">Popis</h2>
                        <p class="mt-2 whitespace-pre-line text-stone-700">{{ $concert->description }}</p>
                    </section>
                @endif

                @if($concert->ticket_url)
                    <a
                        href="{{ $concert->ticket_url }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="mt-8 inline-flex rounded-full bg-teal-700 px-5 py-2.5 text-sm font-semibold text-white hover:bg-teal-800"
                    >
                        Vstupenky
                    </a>
                @endif
            </article>
        </main>
    </body>
</html>
