<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class ContestEndingSoonNotification extends Notification
{
    public $contest;
    public $hoursLeft;

    public function __construct($contest, int $hoursLeft)
    {
        $this->contest = $contest;
        $this->hoursLeft = $hoursLeft;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'kind' => 'ending_soon',
            'contest_id' => $this->contest->id,
            'title' => '⏳ ¡Últimas horas!',
            'message' => 'La trivia "' . ($this->contest->title ?? 'Trivia') . '" termina en aproximadamente ' . $this->hoursLeft . ' hora(s).',
            'url' => route('user.contests.list'),
        ];
    }

    public function toArray($notifiable)
    {
        return $this->toDatabase($notifiable);
    }
}
