<?php

namespace App\Http\Livewire\User;

use App\Models\FollowRequest;
use App\Models\User;
use Livewire\Component;

class FollowerActions extends Component
{
    public User $user;

    public User $viewingUser;

    public array $state = [];

    public function mount()
    {
        $this->updateState();
    }

    public function follow()
    {
        if ($this->viewingUser->follower_preference === 0) {
            $request = new FollowRequest;
            $request->sender()->associate($this->user);
            $request->recipient()->associate($this->viewingUser);
            $request->save();
        } else if ($this->viewingUser->follower_preference === 1) {
            $this->user->follow($this->viewingUser);
        }
        $this->updateState();
    }

    public function cancel()
    {
        $this->viewingUser->followRequestFrom($this->user)->delete();
        $this->updateState();
    }

    public function confirm()
    {
        $this->user->followRequestFrom($this->viewingUser)->delete();
        $this->viewingUser->follow($this->user);
        $this->updateState();
    }

    public function unfollow()
    {
        $this->user->unfollow($this->viewingUser);
        $this->updateState();
    }

    public function render()
    {
        return view('profile.follower-actions');
    }

    private function updateState() {
        $this->state = [
            'canFollow' => (
                $this->viewingUser->follower_preference !== -1 &&
                ! $this->viewingUser->is($this->user) &&
                ! $this->user->isFollowing($this->viewingUser) &&
                ! $this->viewingUser->hasFollowRequestFrom($this->user)
            ),
            'canCancelRequest' => $this->viewingUser->hasFollowRequestFrom($this->user),
            'canConfirmRequest' => $this->user->hasFollowRequestFrom($this->viewingUser),
            'canUnfollow' => $this->user->isFollowing($this->viewingUser),
        ];
    }
}
