@props([
    'name' => 'content',
    'value' => '',
    'editorKey' => 'new',
])

@once
    @push('styles')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/easymde@2.18.0/dist/easymde.min.css">
    @endpush
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/easymde@2.18.0/dist/easymde.min.js"></script>
    @endpush
@endonce

<div
    {{ $attributes->merge(['class' => 'min-h-[260px] rounded-md border border-gray-300']) }}
    wire:key="easymde-{{ $name }}-{{ $editorKey }}"
    wire:ignore
    x-data
    x-init="$nextTick(() => {
        const ta = $refs.mdeta;
        if (ta._easymdeInstance) {
            try { ta._easymdeInstance.toTextArea(); } catch (e) {}
            ta._easymdeInstance = null;
        }
        ta._easymdeInstance = new EasyMDE({
            element: ta,
            spellChecker: false,
            minHeight: '240px',
            uploadImage: true,
            imageUploadEndpoint: '{{ route('admin.editor.upload') }}',
            imageCSRFHeader: true,
            toolbar: ['bold', 'italic', 'heading', '|', 'quote', 'unordered-list', 'ordered-list', '|', 'link', 'image', 'table', '|', 'preview', 'side-by-side', 'fullscreen'],
            placeholder: 'Píšte v Markdownu — použijte tlačítka pro formátování, obrázky nebo tabulky.',
        });
        ta._easymdeInstance.codemirror.on('change', () => $wire.set('{{ $name }}', ta._easymdeInstance.value()));
    })"
>
    <textarea x-ref="mdeta" class="hidden">{{ $value }}</textarea>
</div>
