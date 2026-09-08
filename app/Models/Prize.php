<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prize extends Model
{
    protected $fillable = [
        'contest_id',
        'kind',
        'winner_user_id',
        'name',
        'image_path',
        'quantity',
        'position',
        'won_at',
        'claimed_at',
        'delivered_at',
    ];

    protected $casts = [
        'won_at' => 'datetime',
        'claimed_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    public function contest()
    {
        return $this->belongsTo(Contest::class);
    }

    public function winner()
    {
        return $this->belongsTo(User::class, 'winner_user_id');
    }
}
