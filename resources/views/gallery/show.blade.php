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
    <body class="bg-stone-50 text-stone-900 antialiased">
        <main class="mx-auto max-w-6xl px-4 py-10 sm:px-6 lg:px-8">
            <a href="{{ route('gallery.index') }}" class="text-sm text-teal-700 hover:text-teal-800">&larr; Zpět na galerii</a>
            <h1 class="mt-4 text-3xl font-semibold">{{ $album->title }}</h1>
            @if ($album->description)
                <p class="mt-2 max-w-3xl text-stone-700">{{ $album->description }}</p>
            @endif

            @forelse ($photosByYear as $year => $photos)
                <section class="mt-8">
                    <h2 class="mb-4 text-xl font-semibold">{{ $year }}</h2>
                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ($photos as $photo)
                            <figure class="rounded-xl border border-teal-100 bg-white p-2 shadow-sm">
                                <img src="{{ Storage::url($photo->image_path) }}" alt="{{ $photo->alt_text ?: $photo->title ?: $album->title }}" class="h-60 w-full rounded-lg object-cover">
                                @if ($photo->caption)
                                    <figcaption class="px-2 py-3 text-sm text-stone-700">
                                        {{ $photo->caption }}
                                    </figcaption>
                                @endif
                            </figure>
                        @endforeach
                    </div>
                </section>
            @empty
                <p class="mt-8 text-sm text-stone-600">Album zatím neobsahuje žádné publikované fotky.</p>
            @endforelse
        </main>
    </body>
</html>
