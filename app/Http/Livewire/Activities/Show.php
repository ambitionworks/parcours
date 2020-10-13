<?php

namespace App\Http\Livewire\Activities;

use App\Models\Activity;
use Livewire\Component;

class Show extends Component
{
    public Activity $activity;
    public $editingName;
    public $editing;
    public $name;
    public $description;

    public function mount(Activity $activity)
    {
        $this->name = $activity->name;
        $this->description = $activity->description;
        $this->activity = $activity;
        $this->activity->load('segments');
    }

    public function render()
    {
        return view('activities.show', [
            'laps' => $this->activity->getLaps(),
            'segment_records' => $this->activity->getSegmentRecords()
        ]);
    }

    public function updateDescription()
    {
        $this->activity->update(['description' => $this->description]);
        $this->editing = false;
    }
}
