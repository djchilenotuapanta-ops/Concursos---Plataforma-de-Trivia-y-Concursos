<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Participation extends Model
{
    protected $fillable = [
        'contest_id',
        'user_id',
        'tickets',
        'status',
        'cedula_snapshot',
        'joined_at',
        'attempts_used',
        'attempt_started_at',
        'attempt_expires_at',
        'ending_soon_notified_attempt',
        'ending_soon_notified_at',
        'last_notified_attempt',
        'last_result_attempt',
        'last_correct',
        'last_wrong',
        'last_duration_seconds',
        'last_finished_at'
    ];

    protected $casts = [
        'joined_at' => 'datetime',
        'attempt_started_at' => 'datetime',
        'attempt_expires_at' => 'datetime',

        'ending_soon_notified_attempt' => 'integer',
        'ending_soon_notified_at' => 'datetime',
        'last_notified_attempt' => 'integer',
        'last_result_attempt' => 'integer',
        'last_correct' => 'integer',
        'last_wrong' => 'integer',
        'last_duration_seconds' => 'integer',
        'last_finished_at' => 'datetime'
    ];

    public function contest()
    {
        return $this->belongsTo(Contest::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
