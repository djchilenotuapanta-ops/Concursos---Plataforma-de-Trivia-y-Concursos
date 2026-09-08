<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContestQuestion extends Model
{
    protected $fillable = [
        'contest_id',
        'question',
        'option_a',
        'option_b',
        'option_c',
        'option_d',
        'correct_option',
        'audience',
        'order'
    ];

    protected $casts = [
        'order' => 'integer',
    ];

    public function contest()
    {
        return $this->belongsTo(Contest::class);
    }
}
