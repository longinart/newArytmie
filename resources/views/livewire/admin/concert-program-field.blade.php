<div>
    <label class="mb-1 block text-sm font-medium text-gray-700">Program (Markdown)</label>
    <p class="mb-1 text-xs text-gray-500">Seznam skladeb, odkazy — lze formátovat jako u aktualit.</p>
    <x-admin.easymde-field
        name="program"
        :value="$program"
        :editor-key="($editingConcertId ?? 'n').'-prog'"
    />
</div>
