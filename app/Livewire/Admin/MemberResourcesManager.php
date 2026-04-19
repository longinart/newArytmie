<?php

namespace App\Livewire\Admin;

use App\Models\MemberResource;
use App\Models\MemberResourceFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class MemberResourcesManager extends Component
{
    use WithFileUploads;
    use WithPagination;

    public string $section = 'naslechy';

    public ?int $editingId = null;

    public string $title = '';

    public string $body_markdown = '';

    public int $sort_order = 0;

    /** @var array<int, mixed> */
    public array $uploadFiles = [];

    public function mount(): void
    {
        $sekce = (string) request()->query('sekce', 'naslechy');
        $this->section = in_array($sekce, ['naslechy', 'noty'], true) ? $sekce : 'naslechy';
    }

    public function updatedSection(string $value): void
    {
        if (! in_array($value, ['naslechy', 'noty'], true)) {
            $this->section = 'naslechy';
        }
        $this->resetPage();
        $this->cancelEditing();
    }

    public function save(): void
    {
        $this->validate([
            'section' => ['required', 'in:naslechy,noty'],
            'title' => ['required', 'string', 'max:255'],
            'body_markdown' => ['nullable', 'string'],
            'sort_order' => ['integer', 'min:0'],
            'uploadFiles' => ['nullable', 'array'],
            'uploadFiles.*' => ['file', 'max:102400'],
        ]);

        $payload = [
            'section' => $this->section,
            'title' => $this->title,
            'body_markdown' => $this->body_markdown !== '' ? $this->body_markdown : null,
            'sort_order' => $this->sort_order,
        ];

        if ($this->editingId) {
            $resource = MemberResource::findOrFail($this->editingId);
            $resource->update($payload);
        } else {
            $resource = MemberResource::create($payload);
        }

        foreach ($this->uploadFiles as $upload) {
            if ($upload === null) {
                continue;
            }
            $path = $upload->store('mr/'.$resource->id, 'members_private');
            MemberResourceFile::create([
                'member_resource_id' => $resource->id,
                'original_name' => $upload->getClientOriginalName(),
                'stored_path' => $path,
                'mime' => $upload->getClientMimeType(),
                'size_bytes' => $upload->getSize(),
                'sort_order' => (int) (MemberResourceFile::where('member_resource_id', $resource->id)->max('sort_order') ?? 0) + 1,
            ]);
        }

        $this->uploadFiles = [];
        $this->cancelEditing();
        session()->flash('status', 'Položka byla uložena.');
    }

    public function edit(int $id): void
    {
        $r = MemberResource::findOrFail($id);
        $this->editingId = $r->id;
        $this->section = $r->section;
        $this->title = $r->title;
        $this->body_markdown = $r->body_markdown ?? '';
        $this->sort_order = $r->sort_order;
    }

    public function delete(int $id): void
    {
        $resource = MemberResource::with('files')->findOrFail($id);
        $disk = Storage::disk('members_private');
        foreach ($resource->files as $file) {
            if ($disk->exists($file->stored_path)) {
                $disk->delete($file->stored_path);
            }
        }
        $resource->delete();
        $this->cancelEditing();
        $this->resetPage();
        session()->flash('status', 'Položka byla smazána.');
    }

    public function deleteFile(int $fileId): void
    {
        $file = MemberResourceFile::findOrFail($fileId);
        $disk = Storage::disk('members_private');
        if ($disk->exists($file->stored_path)) {
            $disk->delete($file->stored_path);
        }
        $file->delete();
        session()->flash('status', 'Soubor byl smazán.');
    }

    public function cancelEditing(): void
    {
        $this->reset(['editingId', 'title', 'body_markdown', 'uploadFiles']);
        $this->sort_order = 0;
    }

    public function render()
    {
        $items = MemberResource::query()
            ->where('section', $this->section)
            ->with('files')
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->paginate(15);

        return view('livewire.admin.member-resources-manager', [
            'items' => $items,
        ])->layout('layouts.app');
    }
}
