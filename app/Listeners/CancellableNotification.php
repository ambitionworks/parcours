<?php

namespace App\Listeners;

use Illuminate\Notifications\Events\NotificationSending;

class CancellableNotification
{
    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(NotificationSending $event)
    {
        if (method_exists($event->notification, 'shouldCancel')) {
            return !$event->notification->shouldCancel($event->notifiable);
        }

        return true;
    }
}
