<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class DbNotification extends Notification
{
    public function __construct(private array $data)
    {
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return $this->data;
    }

    public function toArray($notifiable)
    {
        return $this->toDatabase($notifiable);
    }
}
