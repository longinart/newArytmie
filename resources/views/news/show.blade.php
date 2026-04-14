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
    <body class="bg-slate-800 text-slate-100 antialiased">
        <main class="mx-auto max-w-4xl px-4 py-10 sm:px-6 lg:px-8">
            <a href="{{ route('home') }}" class="text-sm font-medium text-orange-400 transition hover:text-orange-300">&larr; Zpět na úvod</a>

            <article class="mt-6 rounded-3xl border border-slate-600/80 bg-slate-900/50 p-6 shadow-sm sm:p-8">
                <p class="text-sm text-orange-400">
                    {{ $news->published_at?->format('d.m.Y H:i') }}
                </p>
                <h1 class="mt-2 text-3xl font-semibold tracking-tight text-white sm:text-4xl">{{ $news->title }}</h1>

                @if($news->excerpt)
                    <p class="mt-4 text-lg leading-8 text-slate-300">{{ $news->excerpt }}</p>
                @endif

                <div
                    class="aktualita-body mt-8 max-w-none text-base leading-7 text-slate-200 [&_p+p]:mt-4 [&_ul]:mt-4 [&_ul]:list-disc [&_ul]:pl-6 [&_ol]:mt-4 [&_ol]:list-decimal [&_ol]:pl-6 [&_li]:mt-1 [&_h2]:mt-8 [&_h2]:text-xl [&_h2]:font-semibold [&_h2]:text-white [&_h3]:mt-6 [&_h3]:text-lg [&_h3]:font-semibold [&_h3]:text-white [&_strong]:font-semibold [&_strong]:text-white [&_a]:font-medium [&_a]:text-orange-400 [&_a]:underline [&_a]:underline-offset-2 [&_a]:decoration-orange-400/40 hover:[&_a]:text-orange-300 [&_blockquote]:mt-6 [&_blockquote]:border-l-4 [&_blockquote]:border-orange-400/70 [&_blockquote]:pl-4 [&_blockquote]:italic [&_blockquote]:text-slate-300"
                >
                    {!! Str::markdown($news->content, ['html_input' => 'strip']) !!}
                </div>
            </article>
        </main>
    </body>
</html>
