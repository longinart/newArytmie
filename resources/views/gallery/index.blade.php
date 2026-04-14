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
                    <a href="{{ route('gallery.show', $album->slug) }}" class="rounded-2xl border border-teal-100 bg-white p-4 shadow-sm hover:shadow">
                        @php $cover = $album->cover_image_path ?: $album->photos->first()?->image_path; @endphp
                        @if ($cover)
                            <img src="{{ Storage::url($cover) }}" alt="{{ $album->title }}" class="h-48 w-full rounded-xl object-cover">
                        @else
                            <div class="h-48 w-full rounded-xl bg-teal-100"></div>
                        @endif
                        <h2 class="mt-4 text-lg font-semibold">{{ $album->title }}</h2>
                        <p class="mt-1 text-sm text-stone-600">{{ $album->photos->count() }} fotek</p>
                        @if ($album->description)
                            <p class="mt-2 text-sm text-stone-700">{{ \Illuminate\Support\Str::limit($album->description, 120) }}</p>
                        @endif
                    </a>
                @empty
                    <p class="text-sm text-stone-600">Galerie je zatím prázdná.</p>
                @endforelse
            </div>
        </main>
    </body>
</html>
