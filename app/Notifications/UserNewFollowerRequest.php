<?php

namespace App\Notifications;

use App\Models\FollowRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserNewFollowerRequest extends Notification
{
    use Queueable;

    public FollowRequest $request;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(FollowRequest $request)
    {
        $this->request = $request;
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
                    ->line(__(':name has requested to follow you.', ['name' => $this->request->sender->name]))
                    ->action('View Profile', route('user.profile', $this->request->sender))
                    ->line('You can review follower requests from your account section on the website.');
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
            'sender_id' => $this->request->sender->id,
        ];
    }
}
