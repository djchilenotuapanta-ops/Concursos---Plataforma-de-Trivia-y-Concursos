<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ContestFinishedForCompany extends Notification
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
            ->line('El concurso ha finalizado.')
            ->action('Ver resultados', url('/'))
            ->line('¡Gracias por usar nuestra plataforma!');
    }

    public function toArray(object $notifiable): array
    {
        return [
        ];
    }
}
