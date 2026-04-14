<!DOCTYPE html>
<html lang="cs">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $news->seo_title ?: $news->title }} | Arytmie Praha</title>
        <meta name="description" content="{{ $news->seo_description ?: \Illuminate\Support\Str::limit(strip_tags($news->excerpt ?: $news->content), 155) }}">
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
    </head>
    <body class="bg-stone-50 text-stone-900 antialiased">
        <main class="mx-auto max-w-4xl px-4 py-10 sm:px-6 lg:px-8">
            <a href="{{ route('home') }}" class="text-sm text-orange-500 hover:text-orange-600">&larr; Zpět na úvod</a>

            <article class="mt-6 rounded-3xl border border-orange-100 bg-white p-6 sm:p-8">
                <p class="text-sm text-orange-500">
                    {{ $news->published_at?->format('d.m.Y H:i') }}
                </p>
                <h1 class="mt-2 text-3xl font-semibold tracking-tight sm:text-4xl">{{ $news->title }}</h1>

                @if($news->excerpt)
                    <p class="mt-4 text-lg leading-8 text-stone-700">{{ $news->excerpt }}</p>
                @endif

                <div class="prose prose-stone mt-8 max-w-none">
                    {!! nl2br(e($news->content)) !!}
                </div>
            </article>
        </main>
    </body>
</html>
