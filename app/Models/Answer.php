<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    protected $fillable = [
        'contest_id',
        'user_id',
        'attempt_no',
        'contest_question_id',
        'selected_option',
        'is_correct',
        'answered_at'
    ];

    protected $casts = [
        'is_correct' => 'boolean',
        'answered_at' => 'datetime',
    ];

    public function contest()
    {
        return $this->belongsTo(Contest::class);
    }

    public function question()
    {
        return $this->belongsTo(ContestQuestion::class, 'contest_question_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
