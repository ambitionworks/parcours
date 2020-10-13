<?php

namespace App\Http\Livewire\Activities;

use App\Models\Activity;
use App\Notifications\ActivityNewLike;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Likers extends Component
{
    public Activity $activity;

    public $inline = false;

    public function toggle()
    {
        Auth::user()->toggleLike($this->activity);
        if ($this->activity->isLikedBy(Auth::user())) {
            $this->activity->user->notify((new ActivityNewLike($this->activity, Auth::user()))->delay(now()->addSeconds(10)));
        }
    }

    public function getUserProperty()
    {
        return Auth::user();
    }

    public function render()
    {
        return view('activities.likers');
    }
}
