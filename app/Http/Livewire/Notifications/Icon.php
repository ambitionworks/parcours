<?php

namespace App\Http\Livewire\Notifications;

use App\Models\Activity;
use App\Models\User;
use App\Notifications\ActivityNewComment;
use App\Notifications\ActivityNewLike;
use App\Notifications\UserNewFollowerRequest;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Icon extends Component
{
    const COUNT = 8;

    public $notifications = [];

    public bool $hasUnread = false;

    public int $moreUnread = 0;

    public bool $opened = false;

    public function hydrate()
    {
        $this->notifications = Auth::user()->notifications()->limit($this::COUNT)->get()->map(function ($row) {
            $view = null;
            $data['read'] = $row->read_at !== null;
            $data['created_at'] = $row->created_at;
            switch ($row->type) {
                case (ActivityNewComment::class):
                    $view = 'notifications.types.activity-comment';
                    $data += [
                        'activity' => Activity::find($row->data['activity_id'])->withoutRelations()->only('name', 'id'),
                        'user' => User::find($row->data['author_id'])->only('name', 'id', 'slug'),
                    ];
                    break;
                case (ActivityNewLike::class):
                    $view = 'notifications.types.activity-like';
                    $data += [
                        'activity' => Activity::find($row->data['activity_id'])->withoutRelations()->only('name', 'id'),
                        'user' => User::find($row->data['liker_id'])->only('name', 'id', 'slug'),
                    ];
                    break;
                case (UserNewFollowerRequest::class):
                    $view = 'notifications.types.user-follower-request';
                    $data += [
                        'user' => User::find($row->data['sender_id'])->only('name', 'id', 'slug'),
                    ];
                    break;
            }
            return $view ? ['view' => $view, 'data' => $data] : null;
        })->filter();

        $this->updateUnreadCount();
    }

    public function mount()
    {
        $this->updateUnreadCount();
    }

    public function toggle()
    {
        if (!$this->opened) {
            Auth::user()->notifications()->limit($this::COUNT)->get()->filter(fn ($notification) => $notification->read_at === null)->each->update(['read_at' => now()]);
            $this->opened = true;
        }
    }

    public function getUserProperty()
    {
        return Auth::user();
    }

    public function render()
    {
        return view('notifications.icon');
    }

    private function updateUnreadCount()
    {
        $localUnreadCount = Auth::user()->notifications()->limit($this::COUNT)->get()->filter(fn ($notification) => $notification->read_at === null)->count();

        $this->hasUnread = $localUnreadCount > 0;
        $this->moreUnread = Auth::user()->unreadNotifications()->count() - $localUnreadCount;
    }
}
