<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ContestFinishedForParticipant extends Notification
{
    use Queueable;

    public function __construct()
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('El concurso en el que participaste ha finalizado.')
            ->action('Ver resultados', url('/'))
            ->line('¡Gracias por participar!');
    }

    public function toArray(object $notifiable): array
    {
        return [
        ];
    }
}
