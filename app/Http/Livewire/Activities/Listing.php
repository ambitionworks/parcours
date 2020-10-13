<?php

namespace App\Http\Livewire\Activities;

use App\Models\Segment;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class Listing extends Component
{
    use WithPagination;

    public $user;
    public $name;
    public $min_distance;
    public $segment;
    public $before_date;
    public $after_date;

    protected $rules = [
        'name' => 'string|nullable',
        'min_distance' => 'numeric|nullable',
        'segment' => 'numeric|nullable',
    ];

    public function updated($field)
    {
        $this->validateOnly($field);
        $this->resetPage();
    }

    public function render()
    {
        $query = $this->user->activities()->whereNotNull('processed_at')->orderByDesc('performed_at');

        $total = $query->count();

        if (!empty($this->name) && strlen($this->name)) {
            $query->whereRaw("LOWER(name) LIKE '%".strtolower($this->name)."%'");
        }

        if (!empty($this->min_distance) && is_numeric($this->min_distance)) {
            $query->where('distance', '>=', $this->min_distance);
        }

        if (!empty($this->segment) && is_numeric($this->segment)) {
            $query->whereHas('segments', function ($query) {
                $query->where('id', $this->segment);
            });
        }

        if (!empty($this->before_date)) {
            $query->whereDate('performed_at', '<=', Carbon::parse($this->before_date));
        }

        if (!empty($this->after_date)) {
            $query->whereDate('performed_at', '>=', Carbon::parse($this->after_date));
        }

        $activities = $query->paginate(20);

        return view('activities.listing', [
            'activities' => $activities,
            'total' => $total,
            'segments' => $this->user->likes(Segment::class)->get()->pluck('name', 'id'),
        ]);
    }
}
