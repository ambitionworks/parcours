<?php

namespace App\Http\Livewire\Segments;

use App\Models\Segment;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Favourite extends Component
{
    public Segment $segment;

    public function toggle()
    {
        Auth::user()->toggleLike($this->segment);
    }

    public function render()
    {
        return view('segments.favourite');
    }
}
