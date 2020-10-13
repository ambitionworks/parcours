<?php

namespace App\Http\Livewire\User;

use App\Models\User;
use Livewire\Component;

class MetricsForm extends Component
{
    public User $user;

    public $state;

    public function mount()
    {
        $this->state = [
            'gender' => $this->user->metric_profile->gender ?? 'male',
            'ftp' => $this->user->metric_profile->ftp ?? null,
            'hr_resting' => $this->user->metric_profile->hr_resting ?? null,
            'hr_max' => $this->user->metric_profile->hr_max ?? null,
            'hr_lt' => $this->user->metric_profile->hr_lt ?? null,
        ];
    }

    public function update()
    {
        $this->user->metric_profile()->updateOrCreate(
            ['user_id' => $this->user->id],
            [
                'gender' => $this->state['gender'],
                'ftp' => $this->state['ftp'],
                'hr_resting' => $this->state['hr_resting'],
                'hr_max' => $this->state['hr_max'],
                'hr_lt' => $this->state['hr_lt'],
            ]
        );

        $this->emit('saved');
    }
    public function render()
    {
        return view('profile.metrics-form');
    }
}
