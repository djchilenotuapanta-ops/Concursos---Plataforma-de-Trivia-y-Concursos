<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // Campos asignables
    protected $fillable = [
        'name','email','password',
        'role','active','company_id','deactivated_at','deactivation_reason','deactivated_by',
        'cedula','birthdate','avatar_path',

        'company_name','razon_social','ruc','phone','address','city','representative_name',
        'website','description','bank_name','bank_account',

        'logo','voucher_image',

        'approved','voucher_approved','rejection_reason',

        'subscription_start','subscription_end',
    ];

    // Campos ocultos en JSON
    protected $hidden = [
        'password',
        'remember_token'
    ];

    // Casts de atributos
    protected $casts = [
        'approved' => 'integer',
        'voucher_approved' => 'integer',
        'active' => 'integer',
        'company_id' => 'integer',
        'deactivated_at' => 'datetime',
        'deactivated_by' => 'integer',
        'subscription_start' => 'datetime',
        'subscription_end' => 'datetime',
        'birthdate' => 'date',
        'last_login_at' => 'datetime'];

    public function contestsOwned()
    {
        // Concursos creados por la empresa
        return $this->hasMany(\App\Models\Contest::class, 'company_id', 'id');
    }

    public function contests()
    {
        // Concursos donde participa
        return $this->belongsToMany(Contest::class, 'participations')
            ->withPivot(['tickets','status','cedula_snapshot','joined_at'])
            ->withTimestamps();
    }

    public function isCompany(): bool
    {
        // Rol empresa
        return $this->role === 'company';
    }

    public function isAdmin(): bool
    {
        // Rol admin
        return in_array($this->role, ['admin', 'administrator', 'administrador'], true);
    }

    public static function adminsQuery()
    {
        // Query para admins
        return static::query()->whereIn('role', ['admin', 'administrator', 'administrador']);
    }

    public function isApproved(): bool
    {
        // Aprobación de cuenta
        return (int)$this->approved === 1;
    }

    public function isVoucherApproved(): bool
    {
        // Voucher aprobado
        return (int)$this->voucher_approved === 1;
    }

    public function hasActiveSubscription(): bool
    {
        // Suscripción vigente
        if (!$this->subscription_end) {
            return false;
        }

        return now()->lessThanOrEqualTo($this->subscription_end);
    }

    public function canCreateContests(): bool
    {
        // Permiso para crear concursos
        return $this->isCompany()
            && (int)($this->active ?? 1) === 1
            && $this->isVoucherApproved()
            && $this->hasActiveSubscription();
    }

    public function getAgeAttribute(): ?int
    {
        // Edad calculada
        if (!$this->birthdate) {
            return null;
        }

        return $this->birthdate->age;
    }

    public function getAvatarUrlAttribute(): string
    {
        // URL de avatar
        if ($this->avatar_path && Storage::disk('public')->exists($this->avatar_path)) {
            return Storage::disk('public')->url($this->avatar_path);
        }

        return asset('images/default-avatar.svg');
    }

    public function company()
    {
        // Empresa relacionada
        return $this->belongsTo(User::class, 'company_id');
    }

    public function companyOwner()
    {
        // Retorna empresa si aplica
        if ($this->role === 'company') return $this;
        return null;
    }

    public function deactivatedBy()
    {
        // Usuario que desactivó
        return $this->belongsTo(User::class, 'deactivated_by');
    }
}
