<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Services\TriviaValidationService;

class Contest extends Model
{
    public function scopeAutoFinalize($query)
    {
        return $query->where('status', 'active')
            ->whereNotNull('end_at')
            ->where('end_at', '<=', now());
    }
    protected $fillable = [
        'company_id',
        'type',
        'title',
        'description',
        'start_at',
        'end_at',
        'draw_at',
        'draw_enabled',
        'draw_top_n',
        'status',
        'ticket_price',
        'winner_method',
        'winner_published_at',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'draw_at' => 'datetime',
        'winner_published_at' => 'datetime',
        'ticket_price' => 'decimal:2',
    ];

    public function company()
    {
        return $this->belongsTo(User::class, 'company_id');
    }

    public function rules()
    {
        return $this->hasOne(ContestRule::class);
    }

    public function rule()
    {
        return $this->rules();
    }

    public function participants()
    {
        return $this->belongsToMany(User::class, 'participations')
            ->withPivot(['tickets','status','cedula_snapshot','joined_at'])
            ->withTimestamps();
    }

    public function prizes()
    {
        return $this->hasMany(Prize::class);
    }

    public function questions()
    {
        return $this->hasMany(ContestQuestion::class);
    }

    public function validatePublishRules(): array
    {
        return TriviaValidationService::validateForPublish($this);
    }
}
