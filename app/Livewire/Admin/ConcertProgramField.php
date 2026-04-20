<?php

namespace App\Livewire\Admin;

use Livewire\Attributes\Isolate;
use Livewire\Attributes\Modelable;
use Livewire\Component;

#[Isolate]
class ConcertProgramField extends Component
{
    #[Modelable]
    public $program = '';

    public ?int $editingConcertId = null;

    public function render()
    {
        return view('livewire.admin.concert-program-field');
    }
}
