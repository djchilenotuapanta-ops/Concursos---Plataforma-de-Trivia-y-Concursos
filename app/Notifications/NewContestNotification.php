<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class NewContestNotification extends Notification
{
    public $contest;

    public function __construct($contest)
    {
        $this->contest = $contest;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'Nuevo concurso disponible',
            'message' => 'Se ha creado el concurso: ' . $this->contest->title,
            'contest_id' => $this->contest->id,
        ];
    }

    public function toArray($notifiable)
    {
        return $this->toDatabase($notifiable);
    }
}
