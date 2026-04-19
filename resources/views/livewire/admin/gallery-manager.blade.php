<div class="py-8">
    <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-semibold text-gray-900">Správa galerie</h1>
            <div class="flex flex-wrap gap-3 text-sm text-gray-600">
                <a href="{{ route('gallery.index') }}" class="hover:text-gray-900">Veřejná galerie</a>
                <a href="{{ route('admin.news.index') }}" class="hover:text-gray-900">Aktuality</a>
                <a href="{{ route('admin.concerts.index') }}" class="hover:text-gray-900">Koncerty</a>
                <a href="{{ route('admin.member-materials.index') }}" class="hover:text-gray-900">Členky</a>
            </div>
        </div>

        @if (session('status'))
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('status') }}</div>
        @endif

        <div class="grid gap-6 lg:grid-cols-2">
            <div class="rounded-xl bg-white p-5 shadow">
                <h2 class="mb-4 text-lg font-medium">{{ $editingAlbumId ? 'Upravit album' : 'Nové album' }}</h2>
                <form wire:submit="saveAlbum" class="space-y-4">
                    <input wire:model.live="album_title" type="text" placeholder="Název alba" class="w-full rounded-lg border-gray-300 text-sm">
                    <input wire:model.live="album_slug" type="text" placeholder="Slug alba" class="w-full rounded-lg border-gray-300 text-sm">
                    <textarea wire:model="album_description" rows="3" placeholder="Popis alba" class="w-full rounded-lg border-gray-300 text-sm"></textarea>
                    <label class="flex items-center gap-2 text-sm">
                        <input wire:model="album_is_published" type="checkbox" class="rounded border-gray-300">
                        Publikovane album
                    </label>
                    <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">Uložit album</button>
                </form>
            </div>

            <div class="rounded-xl bg-white p-5 shadow">
                <h2 class="mb-4 text-lg font-medium">Nahrat fotku</h2>
                <form wire:submit="uploadPhoto" class="space-y-4">
                    <select wire:model.live="selectedAlbumId" class="w-full rounded-lg border-gray-300 text-sm">
                        <option value="">Vyber album</option>
                        @foreach ($albumOptions as $option)
                            <option value="{{ $option->id }}">{{ $option->title }}</option>
                        @endforeach
                    </select>
                    <input wire:model="photo_file" type="file" accept="image/*" class="w-full rounded-lg border-gray-300 text-sm">
                    <input wire:model="photo_title" type="text" placeholder="Název fotky (nepovinné)" class="w-full rounded-lg border-gray-300 text-sm">
                    <input wire:model="photo_alt_text" type="text" placeholder="Alt text (nepovinne)" class="w-full rounded-lg border-gray-300 text-sm">
                    <textarea wire:model="photo_caption" rows="2" placeholder="Popisek (nepovinne)" class="w-full rounded-lg border-gray-300 text-sm"></textarea>
                    <label class="flex items-center gap-2 text-sm">
                        <input wire:model="photo_is_published" type="checkbox" class="rounded border-gray-300">
                        Publikovana fotka
                    </label>
                    <button type="submit" class="rounded-lg bg-orange-500 px-4 py-2 text-sm font-medium text-white hover:bg-orange-600">Nahrat fotku</button>
                </form>
            </div>
        </div>

        <div class="rounded-xl bg-white p-5 shadow">
            <h2 class="mb-4 text-lg font-medium">Alba</h2>
            <div class="space-y-3">
                @forelse ($albums as $album)
                    <div class="flex items-center justify-between rounded-lg border border-gray-200 p-4">
                        <div>
                            <p class="font-medium text-gray-900">{{ $album->title }}</p>
                            <p class="text-xs text-gray-500">/{{ $album->slug }} - {{ $album->is_published ? 'publikováno' : 'koncept' }}</p>
                        </div>
                        <div class="flex gap-2">
                            <button wire:click="editAlbum({{ $album->id }})" class="rounded border border-gray-300 px-2 py-1 text-xs">Upravit</button>
                            <button wire:click="deleteAlbum({{ $album->id }})" wire:confirm="Smazat album i vsechny fotky?" class="rounded border border-red-300 px-2 py-1 text-xs text-red-700">Smazat</button>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">Zatím není žádné album.</p>
                @endforelse
            </div>
            <div class="mt-4">{{ $albums->links() }}</div>
        </div>

        @if ($selectedAlbumId)
            <div class="rounded-xl bg-white p-5 shadow">
                <h2 class="mb-4 text-lg font-medium">Fotky ve vybranem albu</h2>
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    @forelse ($photos as $photo)
                        <div class="rounded-lg border border-gray-200 p-3">
                            <img src="{{ Storage::disk('public')->url($photo->image_path) }}" alt="{{ $photo->alt_text ?: $photo->title }}" class="h-36 w-full rounded object-cover">
                            <p class="mt-2 truncate text-sm font-medium">{{ $photo->title ?: 'Bez nazvu' }}</p>
                            <button wire:click="deletePhoto({{ $photo->id }})" wire:confirm="Smazat fotku?" class="mt-2 rounded border border-red-300 px-2 py-1 text-xs text-red-700">Smazat</button>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 sm:col-span-2 lg:col-span-4">V tomhle albu zatím nejsou fotky.</p>
                    @endforelse
                </div>
            </div>
        @endif
    </div>
</div>
