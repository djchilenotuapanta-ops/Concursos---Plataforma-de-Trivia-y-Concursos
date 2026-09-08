<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TriviaProgress extends Model
{
    protected $table = 'trivia_progress';

    protected $fillable = [
        'contest_id',
        'user_id',
        'current_order',
        'question_started_at',
        'finished',
    ];

    protected $casts = [
        'current_order' => 'integer',
        'question_started_at' => 'datetime',
        'finished' => 'boolean',
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
