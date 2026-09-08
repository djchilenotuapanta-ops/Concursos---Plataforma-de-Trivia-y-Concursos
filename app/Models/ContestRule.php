<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContestRule extends Model
{
    protected $fillable = [
        'contest_id',
        'max_participants',
        'age_group',
        'eligible_top_n',
        'winners_count',
        'seconds_per_question',
        'warning_seconds',
        'trivia_questions_count',
        'trivia_total_seconds',
        'attempts',
        'total_time_minutes',
        'expires_after_join_minutes',
    ];

    protected $casts = [
        'max_participants' => 'integer',
        'eligible_top_n' => 'integer',
        'winners_count' => 'integer',
        'seconds_per_question' => 'integer',
        'warning_seconds' => 'integer',
        'trivia_questions_count' => 'integer',
        'trivia_total_seconds' => 'integer',
        'attempts' => 'integer',
        'total_time_minutes' => 'integer',
        'expires_after_join_minutes' => 'integer',
    ];

    public function contest()
    {
        return $this->belongsTo(Contest::class);
    }
}
