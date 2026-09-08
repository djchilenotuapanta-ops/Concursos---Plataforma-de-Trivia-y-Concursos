<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class AttemptEndingSoonNotification extends Notification
{
    public function __construct(
        public int $contestId,
        public string $contestTitle,
        public int $secondsLeft
    ) {
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        $mins = max(0, (int) floor($this->secondsLeft / 60));
        $secs = max(0, (int) ($this->secondsLeft % 60));
        $mmss = sprintf('%02d:%02d', $mins, $secs);

        return [
            'kind' => 'attempt_ending_soon',
            'contest_id' => $this->contestId,
            'title' => '⚠️ Tiempo por terminar',
            'message' => 'Tu tiempo en "' . $this->contestTitle . '" está por acabar. Te queda: ' . $mmss . '.',
            'url' => route('user.trivia.play', $this->contestId),
        ];
    }

    public function toArray($notifiable)
    {
        return $this->toDatabase($notifiable);
    }
}
