<?php

namespace App\Notifications;

use App\Models\Activity;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ActivityNewLike extends Notification implements ShouldQueue
{
    use Queueable;

    public Activity $activity;

    public User $liker;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Activity $activity, User $liker)
    {
        $this->activity = $activity;
        $this->liker = $liker;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    /**
     * Determine if this notification should be cancelled.
     *
     * @param  mixed  $notifiable
     * @return boolean
     */
    public function shouldCancel($notifiable)
    {
        return !$this->activity->isLikedBy($this->liker);
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->line(__(':name liked your activity :activity.', ['name' => $this->liker->name, 'activity' => $this->activity->name]))
                    ->action('Open Activity', route('activities.show', $this->activity));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'activity_id' => $this->activity->id,
            'liker_id' => $this->liker->id,
        ];
    }
}
