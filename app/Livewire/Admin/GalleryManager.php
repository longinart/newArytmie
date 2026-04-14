<?php

namespace App\Livewire\Admin;

use App\Models\Album;
use App\Models\Photo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class GalleryManager extends Component
{
    use WithFileUploads;
    use WithPagination;

    public ?int $editingAlbumId = null;
    public string $album_title = '';
    public string $album_slug = '';
    public string $album_description = '';
    public bool $album_is_published = true;

    public ?int $selectedAlbumId = null;
    public string $photo_title = '';
    public string $photo_alt_text = '';
    public string $photo_caption = '';
    public $photo_file = null;
    public bool $photo_is_published = true;

    public function updatedAlbumTitle(string $value): void
    {
        if ($this->editingAlbumId === null && $this->album_slug === '') {
            $this->album_slug = Str::slug($value);
        }
    }

    public function saveAlbum(): void
    {
        $validated = $this->validate([
            'album_title' => ['required', 'string', 'max:255'],
            'album_slug' => ['nullable', 'string', 'max:255'],
            'album_description' => ['nullable', 'string'],
            'album_is_published' => ['boolean'],
        ]);

        $slug = $validated['album_slug'] !== '' ? Str::slug($validated['album_slug']) : Str::slug($validated['album_title']);
        $slug = $this->resolveUniqueAlbumSlug($slug, $this->editingAlbumId);

        $album = Album::updateOrCreate(
            ['id' => $this->editingAlbumId],
            [
                'title' => $validated['album_title'],
                'slug' => $slug,
                'description' => $validated['album_description'],
                'is_published' => $validated['album_is_published'],
                'published_at' => $validated['album_is_published'] ? now() : null,
            ]
        );

        $this->selectedAlbumId = $album->id;
        $this->resetAlbumForm();
        session()->flash('status', 'Album byl uložen.');
    }

    public function editAlbum(int $albumId): void
    {
        $album = Album::findOrFail($albumId);
        $this->editingAlbumId = $album->id;
        $this->album_title = $album->title;
        $this->album_slug = $album->slug;
        $this->album_description = $album->description ?? '';
        $this->album_is_published = (bool) $album->is_published;
        $this->selectedAlbumId = $album->id;
    }

    public function deleteAlbum(int $albumId): void
    {
        $album = Album::with('photos')->findOrFail($albumId);
        foreach ($album->photos as $photo) {
            if ($photo->image_path && Storage::disk('public')->exists($photo->image_path)) {
                Storage::disk('public')->delete($photo->image_path);
            }
        }
        $album->delete();

        if ($this->selectedAlbumId === $albumId) {
            $this->selectedAlbumId = null;
        }
        $this->resetPage();
        session()->flash('status', 'Album byl smazán.');
    }

    public function uploadPhoto(): void
    {
        $validated = $this->validate([
            'selectedAlbumId' => ['required', 'integer', 'exists:albums,id'],
            'photo_file' => ['required', 'image', 'max:10240'],
            'photo_title' => ['nullable', 'string', 'max:255'],
            'photo_alt_text' => ['nullable', 'string', 'max:255'],
            'photo_caption' => ['nullable', 'string'],
            'photo_is_published' => ['boolean'],
        ]);

        $path = $this->photo_file->store('gallery/'.$this->selectedAlbumId, 'public');

        Photo::create([
            'album_id' => $validated['selectedAlbumId'],
            'title' => $validated['photo_title'],
            'alt_text' => $validated['photo_alt_text'],
            'image_path' => $path,
            'caption' => $validated['photo_caption'],
            'is_published' => $validated['photo_is_published'],
            'sort_order' => (int) (Photo::where('album_id', $validated['selectedAlbumId'])->max('sort_order') ?? 0) + 1,
        ]);

        $this->resetPhotoForm();
        session()->flash('status', 'Fotka byla nahrána.');
    }

    public function deletePhoto(int $photoId): void
    {
        $photo = Photo::findOrFail($photoId);
        if ($photo->image_path && Storage::disk('public')->exists($photo->image_path)) {
            Storage::disk('public')->delete($photo->image_path);
        }
        $photo->delete();
        session()->flash('status', 'Fotka byla smazána.');
    }

    private function resetAlbumForm(): void
    {
        $this->reset(['editingAlbumId', 'album_title', 'album_slug', 'album_description', 'album_is_published']);
        $this->album_is_published = true;
    }

    private function resetPhotoForm(): void
    {
        $this->reset(['photo_title', 'photo_alt_text', 'photo_caption', 'photo_file', 'photo_is_published']);
        $this->photo_is_published = true;
    }

    private function resolveUniqueAlbumSlug(string $slug, ?int $ignoreId = null): string
    {
        $base = $slug !== '' ? $slug : 'album';
        $candidate = $base;
        $counter = 1;

        while (
            Album::query()
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
        $albums = Album::query()->latest()->paginate(10);
        $photos = collect();

        if ($this->selectedAlbumId) {
            $photos = Photo::query()
                ->where('album_id', $this->selectedAlbumId)
                ->latest()
                ->limit(50)
                ->get();
        }

        return view('livewire.admin.gallery-manager', [
            'albums' => $albums,
            'photos' => $photos,
            'albumOptions' => Album::query()->orderBy('title')->get(['id', 'title']),
        ])->layout('layouts.app');
    }
}
