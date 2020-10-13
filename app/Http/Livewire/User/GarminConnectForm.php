<?php

namespace App\Http\Livewire\User;

use App\Models\User;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;

class GarminConnectForm extends Component
{
    /**
     * The component's state.
     *
     * @var array
     */
    public $state = [];

    /**
     * The user for this component.
     *
     * @var User
     */
    public User $user;

    public bool $confirmingDisable = false;

    public function mount()
    {
        $this->state = [
            'email' => $this->user->garmin_connect_profile->email ?? '',
            'password' => '',
        ];
    }

    public function update()
    {
        $this->resetErrorBag();

        Validator::make($this->state, [
            'email' => 'required|email',
            'password' => 'required',
        ])->validate();

        $this->user->garmin_connect_profile()->updateOrCreate(
            ['user_id' => $this->user->id],
            ['email' => $this->state['email'], 'password' => Crypt::encrypt($this->state['password'])]
        );

        $this->emit('saved');
    }

    public function confirmDisable() {
        $this->confirmingDisable = true;
    }

    public function disable()
    {
        $this->user->garmin_connect_profile->delete();

        $this->state = [
            'email' => '',
            'password' => '',
        ];
        $this->resetErrorBag();
        $this->confirmingDisable = false;

        $this->emit('disabled');
    }

    public function render()
    {
        return view('profile.garmin-connect-form');
    }
}
