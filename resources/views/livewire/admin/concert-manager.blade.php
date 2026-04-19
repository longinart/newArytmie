<div class="py-8">
    <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-semibold text-gray-900">Správa koncertů</h1>
            <div class="flex flex-wrap gap-3 text-sm text-gray-600">
                <a href="{{ route('admin.news.index') }}" class="hover:text-gray-900">Aktuality</a>
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
                    {{ $editingId ? 'Upravit koncert' : 'Nový koncert' }}
                </h2>

                <form wire:submit="save" class="space-y-4">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Název koncertu</label>
                        <input wire:model.live="title" type="text" class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('title') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Slug</label>
                        <input wire:model.live="slug" type="text" class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('slug') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Zacatek</label>
                            <input wire:model="starts_at" type="datetime-local" class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('starts_at') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Konec</label>
                            <input wire:model="ends_at" type="datetime-local" class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('ends_at') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Misto</label>
                            <input wire:model="venue_name" type="text" class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('venue_name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Mesto</label>
                            <input wire:model="city" type="text" class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('city') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Adresa mista</label>
                        <input wire:model="venue_address" type="text" class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Program (Markdown)</label>
                        <p class="mb-1 text-xs text-gray-500">Seznam skladeb, odkazy — lze formátovat jako u aktualit.</p>
                        <x-admin.easymde-field name="program" :value="$program" :editor-key="($editingId ?? 'n').'-prog'" />
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Popis (Markdown)</label>
                        <p class="mb-1 text-xs text-gray-500">Delší text o koncertu, obrázky z editoru.</p>
                        <x-admin.easymde-field name="description" :value="$description" :editor-key="($editingId ?? 'n').'-desc'" />
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">URL vstupenek</label>
                        <input wire:model="ticket_url" type="url" class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('ticket_url') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">SEO title</label>
                            <input wire:model="seo_title" type="text" class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">SEO description</label>
                            <textarea wire:model="seo_description" rows="2" class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                        </div>
                    </div>

                    <label class="flex items-center gap-2 text-sm text-gray-700">
                        <input wire:model="is_published" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                        Publikovano
                    </label>

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
                <h2 class="mb-4 text-lg font-medium text-gray-900">Seznam koncertů</h2>
                <div class="space-y-3">
                    @forelse ($concerts as $concert)
                        <div class="rounded-lg border border-gray-200 p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="font-medium text-gray-900">{{ $concert->title }}</p>
                                    <p class="text-xs text-gray-500">
                                        {{ $concert->starts_at?->format('d.m.Y H:i') }} - {{ $concert->city }}
                                    </p>
                                    <p class="text-xs text-gray-500">{{ $concert->venue_name }}</p>
                                    <p class="mt-1 text-xs text-gray-500">{{ $concert->is_published ? 'Publikovano' : 'Koncept' }}</p>
                                </div>
                                <div class="flex flex-wrap gap-2">
                                    <button wire:click="edit({{ $concert->id }})" class="rounded border border-gray-300 px-2 py-1 text-xs text-gray-700 hover:bg-gray-50">Upravit</button>
                                    <button wire:click="togglePublished({{ $concert->id }})" class="rounded border border-indigo-300 px-2 py-1 text-xs text-indigo-700 hover:bg-indigo-50">
                                        {{ $concert->is_published ? 'Skryt' : 'Publikovat' }}
                                    </button>
                                    <button
                                        wire:click="delete({{ $concert->id }})"
                                        wire:confirm="Opravdu chcete smazat tento koncert?"
                                        class="rounded border border-red-300 px-2 py-1 text-xs text-red-700 hover:bg-red-50"
                                    >
                                        Smazat
                                    </button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">Zatím není žádný koncert.</p>
                    @endforelse
                </div>

                @if ($concerts->hasPages())
                    <div class="mt-4">
                        {{ $concerts->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
