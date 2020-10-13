<?php

namespace App\Http\Livewire\User;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class FollowerRequestsForm extends Component
{
    public User $user;

    public $status = [];

    public function confirm(int $id)
    {
        $requestUser = User::find($id);
        $requestUser->follow($this->user);
        $this->user->followRequestFrom($requestUser)->delete();
        $this->status[$id] = true;
    }

    public function deny(int $id)
    {
        $requestUser = User::find($id);
        $this->user->followRequestFrom($requestUser)->delete();
        $this->status[$id] = false;
    }

    public function render()
    {
        return view('profile.follower-requests-form');
    }
}
