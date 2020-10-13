<?php

namespace App\Http\Livewire\Segments;

use App\Models\Segment;
use Livewire\Component;
use Livewire\WithPagination;

class Search extends Component
{
    use WithPagination;

    public ?string $name;
    public $lat;
    public $lng;
    public $radius;
    public $distance;

    public function render()
    {
        if (isset($this->lat, $this->lng, $this->radius)) {
            $query = Segment::with(['user_efforts' => function ($query) {
                $query->orderBy('elapsed', 'asc');
            }])->whereRaw('ST_DWithin(start_point, ST_MakePoint(?, ?), ?)', [$this->lng, $this->lat, $this->radius]);

            if ($this->name) {
                $query->whereRaw("LOWER(name) LIKE '%".strtolower($this->name)."%'");
            }

            if ($this->distance) {
                $query->where('distance', '>=', $this->distance);
            }


            $results = $query->paginate();
        }
        return view('segments.search', [
            'results' => $results ?? [],
        ]);
    }
}
