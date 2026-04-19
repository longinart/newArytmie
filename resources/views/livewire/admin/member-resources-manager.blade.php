<div class="py-8">
    <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <h1 class="text-2xl font-semibold text-gray-900">Materiály pro členky (Náslechy / Noty)</h1>
            <div class="flex flex-wrap gap-3 text-sm text-gray-600">
                <a href="{{ url('/') }}" class="hover:text-gray-900">Veřejný web</a>
                <a href="{{ route('admin.news.index') }}" class="hover:text-gray-900">Aktuality</a>
                <a href="{{ route('admin.concerts.index') }}" class="hover:text-gray-900">Koncerty</a>
                <a href="{{ route('admin.gallery.index') }}" class="hover:text-gray-900">Galerie</a>
            </div>
        </div>

        @if (session('status'))
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                {{ session('status') }}
            </div>
        @endif

        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
            <label class="mb-2 block text-sm font-medium text-gray-700">Sekce na webu</label>
            <select wire:model.live="section" class="rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="naslechy">Náslechy</option>
                <option value="noty">Noty</option>
            </select>
            <p class="mt-2 text-xs text-gray-500">
                Vyberte, zda upravujete obsah stránky „Náslechy“ nebo „Noty“ v členské sekci (po hesle).
            </p>
        </div>

        <div class="grid gap-6 lg:grid-cols-2">
            <div class="rounded-xl bg-white p-5 shadow">
                <h2 class="mb-4 text-lg font-medium text-gray-900">
                    {{ $editingId ? 'Upravit položku' : 'Nová položka' }}
                </h2>

                <form wire:submit="save" class="space-y-4">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Nadpis</label>
                        <input wire:model="title" type="text" class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('title') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Text (Markdown)</label>
                        <p class="mb-1 text-xs text-gray-500">
                            Jednoduché formátování: prázdný řádek = odstavec, <code class="rounded bg-gray-100 px-1">**tučně**</code>,
                            odrážky řádky začínající <code class="rounded bg-gray-100 px-1">- </code>. Odkaz: <code class="rounded bg-gray-100 px-1">[text](https://…)</code>.
                        </p>
                        <textarea wire:model="body_markdown" rows="14" class="w-full rounded-lg border-gray-300 font-mono text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Volitelný úvodní text…"></textarea>
                        @error('body_markdown') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Připojit soubory (PDF, MP3, …)</label>
                        <input wire:model="uploadFiles" type="file" multiple class="block w-full text-sm text-gray-600 file:mr-4 file:rounded-lg file:border-0 file:bg-indigo-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-indigo-700 hover:file:bg-indigo-100">
                        @error('uploadFiles.*') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        <p class="mt-1 text-xs text-gray-500">Max. cca 100 MB na soubor. Členky si soubor stáhnou po přihlášení heslem.</p>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
                            Uložit
                        </button>
                        @if ($editingId)
                            <button type="button" wire:click="cancelEditing" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                                Zrušit
                            </button>
                        @endif
                    </div>
                </form>
            </div>

            <div class="rounded-xl bg-white p-5 shadow">
                <h2 class="mb-1 text-lg font-medium text-gray-900">Seznam v této sekci</h2>
                <p class="mb-4 text-xs text-gray-500">Nejnovější příspěvek je vždy nahoře (řadí se podle data vytvoření).</p>
                <div class="space-y-4" wire:key="mr-list-{{ $section }}-p{{ $items->currentPage() }}">
                    @forelse ($items as $item)
                        <div wire:key="mr-item-{{ $item->id }}" class="rounded-lg border border-gray-200 p-4 text-sm">
                            <div class="flex flex-wrap items-start justify-between gap-2">
                                <div>
                                    <p class="font-medium text-gray-900">{{ $item->title }}</p>
                                    <p class="text-xs text-gray-500">{{ $item->created_at?->format('d.m.Y H:i') }}</p>
                                    @if ($item->files->isNotEmpty())
                                        <ul class="mt-2 list-inside list-disc text-xs text-gray-600">
                                            @foreach ($item->files as $f)
                                                <li wire:key="mr-file-{{ $f->id }}" class="flex flex-wrap items-center gap-2">
                                                    {{ $f->original_name }}
                                                    <button type="button" wire:click="deleteFile({{ $f->id }})" class="text-red-600 hover:underline">Smazat soubor</button>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </div>
                                <div class="flex gap-2">
                                    <button type="button" wire:click="edit({{ $item->id }})" class="rounded border border-gray-300 px-2 py-1 text-xs text-gray-700 hover:bg-gray-50">Upravit</button>
                                    <button type="button" wire:click="delete({{ $item->id }})" wire:confirm="Smazat celou položku včetně souborů?" class="rounded border border-red-300 px-2 py-1 text-xs text-red-700 hover:bg-red-50">Smazat</button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">Zatím žádné položky.</p>
                    @endforelse
                </div>
                @if ($items->hasPages())
                    <div class="mt-4">{{ $items->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</div>
