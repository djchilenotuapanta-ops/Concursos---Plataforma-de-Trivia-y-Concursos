<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ContestPublished extends Notification
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
            ->line('Se ha publicado un nuevo concurso.')
            ->action('Ver concurso', url('/'))
            ->line('¡Gracias por usar nuestra plataforma!');
    }

    public function toArray(object $notifiable): array
    {
        return [
        ];
    }
}
