<?php

namespace App\Livewire\Admin;

use App\Models\Concert;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Isolate;
use Livewire\Attributes\Modelable;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Isolate]
class ConcertCoverUpload extends Component
{
    use WithFileUploads;

    #[Modelable]
    public $coverImage = null;

    public ?int $editingConcertId = null;

    public ?string $existingCoverPath = null;

    public function mount(?int $editingConcertId = null): void
    {
        $this->editingConcertId = $editingConcertId;
        $this->existingCoverPath = $this->readPathFromDatabase();
    }

    public function removeCoverImage(): void
    {
        if ($this->coverImage !== null) {
            $this->coverImage = null;

            return;
        }

        if ($this->editingConcertId === null) {
            $this->existingCoverPath = null;

            return;
        }

        $concert = Concert::find($this->editingConcertId);
        if ($concert === null || $concert->cover_image_path === null) {
            $this->existingCoverPath = null;

            return;
        }

        $disk = Storage::disk('public');
        if ($disk->exists($concert->cover_image_path)) {
            $disk->delete($concert->cover_image_path);
        }
        $concert->update(['cover_image_path' => null]);
        $this->existingCoverPath = null;
    }

    protected function readPathFromDatabase(): ?string
    {
        if ($this->editingConcertId === null) {
            return null;
        }

        return Concert::whereKey($this->editingConcertId)->value('cover_image_path');
    }

    public function render()
    {
        return view('livewire.admin.concert-cover-upload');
    }
}
