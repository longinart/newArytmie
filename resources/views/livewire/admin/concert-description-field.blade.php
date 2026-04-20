<div>
    <label class="mb-1 block text-sm font-medium text-gray-700">Popis (Markdown)</label>
    <p class="mb-1 text-xs text-gray-500">Delší text o koncertu, obrázky z editoru.</p>
    <x-admin.easymde-field
        name="description"
        :value="$description"
        :editor-key="($editingConcertId ?? 'n').'-desc'"
    />
</div>
