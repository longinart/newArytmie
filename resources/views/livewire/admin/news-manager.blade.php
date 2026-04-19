<div class="py-8">
    <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-semibold text-gray-900">Správa aktualit</h1>
            <div class="flex flex-wrap gap-3 text-sm text-gray-600">
                <a href="{{ url('/') }}" class="hover:text-gray-900">Veřejný web</a>
                <a href="{{ route('admin.concerts.index') }}" class="hover:text-gray-900">Koncerty</a>
                <a href="{{ route('admin.gallery.index') }}" class="hover:text-gray-900">Galerie</a>
                <a href="{{ route('admin.member-materials.index') }}" class="hover:text-gray-900">Členky</a>
            </div>
        </div>

        @if (session('status'))
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                {{ session('status') }}
            </div>
        @endif

        <div class="grid gap-6 lg:grid-cols-2">
            <div class="rounded-xl bg-white p-5 shadow">
                <h2 class="mb-4 text-lg font-medium text-gray-900">
                    {{ $editingId ? 'Upravit aktualitu' : 'Nová aktualita' }}
                </h2>

                <form wire:submit="save" class="space-y-4">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Nadpis</label>
                        <input wire:model.live="title" type="text" class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('title') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Slug</label>
                        <input wire:model.live="slug" type="text" class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('slug') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Perex</label>
                        <textarea wire:model="excerpt" rows="2" class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Obsah (Markdown editor)</label>
                        <p class="mb-2 text-xs text-gray-500">Formátování, odkazy, obrázky (nahrání na server), náhled v editoru. Zobrazí se na webu jako článek.</p>
                        <x-admin.easymde-field name="content" :value="$content" :editor-key="$editingId ?? 'new'" />
                        @error('content') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">SEO title</label>
                            <input wire:model="seo_title" type="text" class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Publikovat od</label>
                            <input wire:model="published_at" type="datetime-local" class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">SEO description</label>
                        <textarea wire:model="seo_description" rows="2" class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                    </div>

                    <div class="space-y-2">
                        <label class="flex items-center gap-2 text-sm text-gray-700">
                            <input wire:model.live="is_published" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                            Publikováno
                        </label>
                        <p class="text-xs leading-relaxed text-gray-500">
                            Úvodní stránka zobrazí aktualitu jen pokud je „Publikováno“ zaškrtnuté a datum „Publikovat od“ není v budoucnosti
                            (prázdné datum při publikování = ihned).
                        </p>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
                            Uložit
                        </button>
                        @if ($editingId)
                            <button type="button" wire:click="cancelEditing" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                                Zrušit úpravy
                            </button>
                        @endif
                    </div>
                </form>
            </div>

            <div class="rounded-xl bg-white p-5 shadow">
                <h2 class="mb-4 text-lg font-medium text-gray-900">Seznam aktualit</h2>
                <div class="space-y-3">
                    @forelse ($newsItems as $item)
                        <div class="rounded-lg border border-gray-200 p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="font-medium text-gray-900">{{ $item->title }}</p>
                                    <p class="text-xs text-gray-500">/{{ $item->slug }}</p>
                                    <p class="mt-1 text-xs text-gray-500">
                                        {{ $item->is_published ? 'Publikováno' : 'Koncept' }}
                                        @if($item->published_at)
                                            - {{ $item->published_at->format('d.m.Y H:i') }}
                                        @endif
                                    </p>
                                </div>
                                <div class="flex flex-wrap gap-2">
                                    <button wire:click="edit({{ $item->id }})" class="rounded border border-gray-300 px-2 py-1 text-xs text-gray-700 hover:bg-gray-50">Upravit</button>
                                    <button wire:click="togglePublished({{ $item->id }})" class="rounded border border-indigo-300 px-2 py-1 text-xs text-indigo-700 hover:bg-indigo-50">
                                        {{ $item->is_published ? 'Skrýt' : 'Publikovat' }}
                                    </button>
                                    <button
                                        wire:click="delete({{ $item->id }})"
                                        wire:confirm="Opravdu chcete smazat tuto aktualitu?"
                                        class="rounded border border-red-300 px-2 py-1 text-xs text-red-700 hover:bg-red-50"
                                    >
                                        Smazat
                                    </button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">Zatím není žádná aktualita.</p>
                    @endforelse
                </div>

                @if ($newsItems->hasPages())
                    <div class="mt-4">
                        {{ $newsItems->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
