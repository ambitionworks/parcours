<?php

namespace App\Http\Livewire\User;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class FollowerPreferenceForm extends Component
{
    /**
     * The component's state.
     *
     * @var array
     */
    public $state = [];

    public function mount()
    {
        $this->state = Auth::user()->withoutRelations()->only(['follower_preference']);
    }

    public function updatePreferences()
    {
        Auth::user()->forceFill([
            'follower_preference' => $this->state['follower_preference'] ?? 1,
        ])->save();

        $this->emit('saved');
    }

    public function render()
    {
        return view('profile.follower-preference-form');
    }
}
