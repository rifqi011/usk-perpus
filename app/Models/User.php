<?php

namespace App\Models;

use App\Enums\AccountType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'account_type',
        'email_verified_at',
        'is_active',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
            'account_type' => AccountType::class,
            'is_active' => 'boolean',
        ];
    }

    // Relations
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class)->withTimestamps();
    }

    public function adminProfile(): HasOne
    {
        return $this->hasOne(AdminProfile::class);
    }

    public function memberProfile(): HasOne
    {
        return $this->hasOne(MemberProfile::class);
    }

    // Scopes
    public function scopeAdmins($query)
    {
        return $query->where('account_type', AccountType::ADMIN);
    }

    public function scopeMembers($query)
    {
        return $query->where('account_type', AccountType::MEMBER);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Helper methods
    public function isAdmin(): bool
    {
        return $this->account_type === AccountType::ADMIN;
    }

    public function isMember(): bool
    {
        return $this->account_type === AccountType::MEMBER;
    }

    public function isSuperAdmin(): bool
    {
        return $this->isAdmin() && $this->roles()->where('code', 'superadmin')->exists();
    }

    public function hasRole(string $roleCode): bool
    {
        return $this->roles()->where('code', $roleCode)->exists();
    }

    public function updateLastLogin(): void
    {
        $this->update(['last_login_at' => now()]);
    }
    
    // Filament avatar
    public function getFilamentAvatarUrl(): ?string
    {
        if ($this->isAdmin() && $this->adminProfile && $this->adminProfile->photo) {
            return asset('storage/' . $this->adminProfile->photo);
        }
        
        if ($this->isMember() && $this->memberProfile && $this->memberProfile->photo) {
            return asset('storage/' . $this->memberProfile->photo);
        }
        
        return null;
    }
}
