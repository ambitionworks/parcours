<?php

namespace App\Http\Livewire\Segments;

use App\Models\ActivitySegment;
use App\Models\Segment;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class ActivityUserEffortList extends Component
{
    use WithPagination;

    public User $user;

    public Segment $segment;

    public function render()
    {
        $efforts = ActivitySegment::segment($this->segment)
            ->byUser($this->user)
            ->orderBy('start_time', 'desc')
            ->orderBy('activity_id', 'desc')
            ->paginate(5);

        $records = ActivitySegment::segment($this->segment)
            ->byUser($this->user)
            ->orderBy('elapsed', 'asc')
            ->limit(3)
            ->get();

        return view('segments.activity-user-effort-list', [
            'efforts' => $efforts,
            'records' => $records,
        ]);
    }
}
