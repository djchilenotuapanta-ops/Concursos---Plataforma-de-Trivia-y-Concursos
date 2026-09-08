<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;

class WinnerNotification extends Notification implements ShouldQueue
{
        use Queueable;

public $contest;
    public $prize;

    public function __construct($contest, $prize)
    {
        $this->contest = $contest;
        $this->prize = $prize;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => '¡Felicidades! Has ganado',
            'message' => 'Ganaste el concurso "' . $this->contest->title .
                         '" - Premio: ' . ($this->prize->name ?? 'Premio'),
        ];
    }

    public function toArray($notifiable)
    {
        return $this->toDatabase($notifiable);
    }
}
