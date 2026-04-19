<div>
    <label class="mb-1 block text-sm font-medium text-gray-700">Titulní obrázek</label>
    <p class="mb-1 text-xs text-gray-500">
        Volitelný náhled u detailu koncertu — kliknutím se zvětší (stejně jako fotky v galerii).
    </p>
    <input
        wire:model="coverImage"
        type="file"
        accept="image/*"
        class="block w-full text-sm text-gray-600 file:mr-4 file:rounded-lg file:border-0 file:bg-indigo-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-indigo-700 hover:file:bg-indigo-100"
    >
    <div class="mt-3 flex flex-wrap items-end gap-4">
        @if ($coverImage && method_exists($coverImage, 'temporaryUrl'))
            <div>
                <img
                    src="{{ $coverImage->temporaryUrl() }}"
                    alt=""
                    class="h-36 max-w-sm rounded-lg border border-gray-200 object-cover"
                >
                <button type="button" wire:click="removeCoverImage" class="mt-1 text-xs text-red-600 hover:underline">
                    Zrušit nový obrázek
                </button>
            </div>
        @elseif ($existingCoverPath)
            <div>
                <img
                    src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($existingCoverPath) }}"
                    alt=""
                    class="h-36 max-w-sm rounded-lg border border-gray-200 object-cover"
                >
                <button type="button" wire:click="removeCoverImage" class="mt-1 text-xs text-red-600 hover:underline">
                    Odebrat obrázek
                </button>
            </div>
        @endif
    </div>
</div>
