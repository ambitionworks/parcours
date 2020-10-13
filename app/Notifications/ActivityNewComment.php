<?php

namespace App\Notifications;

use App\Models\Activity;
use App\Models\Comment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ActivityNewComment extends Notification
{
    use Queueable;

    public Activity $activity;

    public Comment $comment;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Comment $comment)
    {
        $this->activity = $comment->commentable;
        $this->comment = $comment;
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
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->line(__(':name has left a comment on your activity :activity', ['name' => $this->comment->user->name, 'activity' => $this->activity->name]))
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
            'author_id' => $this->comment->user->id,
        ];
    }
}
