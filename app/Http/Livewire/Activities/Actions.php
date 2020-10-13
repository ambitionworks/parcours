<?php

namespace App\Http\Livewire\Activities;

use Livewire\Component;

class Actions extends Component
{
    public $activity;

    public $confirmingDelete = false;

    public function confirmDelete()
    {
        $this->confirmingDelete = true;
    }

    public function delete()
    {
        $this->activity->delete();

        return redirect()->route('activities.index');
    }

    public function render()
    {
        return view('activities.actions');
    }
}
