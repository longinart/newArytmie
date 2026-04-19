<?php

namespace App\Livewire\Admin;

use App\Models\Concert;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class ConcertManager extends Component
{
    use WithFileUploads;
    use WithPagination;

    public ?int $editingId = null;

    public string $title = '';

    public string $slug = '';

    public string $starts_at = '';

    public string $ends_at = '';

    public string $venue_name = '';

    public string $venue_address = '';

    public string $city = '';

    public string $program = '';

    public string $description = '';

    public string $ticket_url = '';

    public string $seo_title = '';

    public string $seo_description = '';

    public bool $is_published = false;

    /**
     * @var mixed
     */
    public $coverImage = null;

    public function updatedTitle(string $value): void
    {
        if ($this->editingId === null && $this->slug === '') {
            $this->slug = Str::slug($value);
        }
    }

    public function save(): void
    {
        $validated = $this->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'venue_name' => ['required', 'string', 'max:255'],
            'venue_address' => ['nullable', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'program' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'ticket_url' => ['nullable', 'url', 'max:255'],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'string'],
            'is_published' => ['boolean'],
            'coverImage' => ['nullable', 'image', 'max:10240'],
        ]);

        $upload = $validated['coverImage'] ?? null;
        unset($validated['coverImage']);

        $slug = $validated['slug'] !== '' ? Str::slug($validated['slug']) : Str::slug($validated['title']);
        $validated['slug'] = $this->resolveUniqueSlug($slug, $this->editingId);
        $validated['ends_at'] = $validated['ends_at'] !== '' ? $validated['ends_at'] : null;
        $validated['ticket_url'] = $validated['ticket_url'] !== '' ? $validated['ticket_url'] : null;

        $concert = Concert::updateOrCreate(
            ['id' => $this->editingId],
            $validated
        );

        if ($upload !== null) {
            $disk = Storage::disk('public');
            if ($concert->cover_image_path && $disk->exists($concert->cover_image_path)) {
                $disk->delete($concert->cover_image_path);
            }
            $path = $upload->store('concert-covers/'.$concert->id, 'public');
            $concert->update(['cover_image_path' => $path]);
        }

        $this->coverImage = null;
        $this->resetForm();
        session()->flash('status', 'Koncert byl uložen.');
    }

    public function edit(int $id): void
    {
        $concert = Concert::findOrFail($id);

        $this->editingId = $concert->id;
        $this->title = $concert->title;
        $this->slug = $concert->slug;
        $this->starts_at = $concert->starts_at?->format('Y-m-d\TH:i') ?? '';
        $this->ends_at = $concert->ends_at?->format('Y-m-d\TH:i') ?? '';
        $this->venue_name = $concert->venue_name;
        $this->venue_address = $concert->venue_address ?? '';
        $this->city = $concert->city;
        $this->program = $concert->program ?? '';
        $this->description = $concert->description ?? '';
        $this->ticket_url = $concert->ticket_url ?? '';
        $this->seo_title = $concert->seo_title ?? '';
        $this->seo_description = $concert->seo_description ?? '';
        $this->is_published = (bool) $concert->is_published;
        $this->coverImage = null;
    }

    public function delete(int $id): void
    {
        $concert = Concert::findOrFail($id);
        if ($concert->cover_image_path) {
            $disk = Storage::disk('public');
            if ($disk->exists($concert->cover_image_path)) {
                $disk->delete($concert->cover_image_path);
            }
        }
        $concert->delete();
        $this->resetPage();
        session()->flash('status', 'Koncert byl smazán.');
    }

    public function togglePublished(int $id): void
    {
        $concert = Concert::findOrFail($id);
        $concert->is_published = ! $concert->is_published;
        $concert->save();
    }

    public function cancelEditing(): void
    {
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->reset([
            'editingId',
            'title',
            'slug',
            'starts_at',
            'ends_at',
            'venue_name',
            'venue_address',
            'city',
            'program',
            'description',
            'ticket_url',
            'seo_title',
            'seo_description',
            'is_published',
        ]);
        $this->coverImage = null;
    }

    private function resolveUniqueSlug(string $slug, ?int $ignoreId = null): string
    {
        $base = $slug !== '' ? $slug : 'koncert';
        $candidate = $base;
        $counter = 1;

        while (
            Concert::query()
                ->where('slug', $candidate)
                ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $candidate = $base.'-'.$counter;
            $counter++;
        }

        return $candidate;
    }

    public function render()
    {
        return view('livewire.admin.concert-manager', [
            'concerts' => Concert::query()->latest('starts_at')->paginate(10),
        ])->layout('layouts.app');
    }
}
