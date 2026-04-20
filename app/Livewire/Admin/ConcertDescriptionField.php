<?php

namespace App\Livewire\Admin;

use Livewire\Attributes\Isolate;
use Livewire\Attributes\Modelable;
use Livewire\Component;

#[Isolate]
class ConcertDescriptionField extends Component
{
    #[Modelable]
    public $description = '';

    public ?int $editingConcertId = null;

    public function render()
    {
        return view('livewire.admin.concert-description-field');
    }
}
