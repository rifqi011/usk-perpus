<?php

namespace App\Models;

use App\Enums\Gender;
use App\Enums\MembershipStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class MemberProfile extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'member_code',
        'identity_number',
        'full_name',
        'gender',
        'birth_place',
        'birth_date',
        'phone_number',
        'address',
        'photo',
        'registration_date',
        'membership_status',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'registration_date' => 'date',
            'gender' => Gender::class,
            'membership_status' => MembershipStatus::class,
        ];
    }

    // Relations
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class, 'member_id');
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class, 'member_id');
    }

    public function fines(): HasMany
    {
        return $this->hasMany(Fine::class, 'member_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('membership_status', MembershipStatus::ACTIVE);
    }

    public function scopePending($query)
    {
        return $query->where('membership_status', MembershipStatus::PENDING);
    }

    // Helper methods
    public function isActive(): bool
    {
        return $this->membership_status === MembershipStatus::ACTIVE;
    }

    public function canBorrow(): bool
    {
        return $this->isActive();
    }

    public function getActiveLoanCount(): int
    {
        return $this->loans()
            ->whereIn('status', ['borrowed', 'overdue'])
            ->count();
    }

    public function getTotalUnpaidFines(): float
    {
        return $this->fines()
            ->where('status', 'unpaid')
            ->sum('amount');
    }
}
