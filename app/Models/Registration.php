<?php

namespace App\Models;

use App\Enums\RegistrationStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Registration extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'full_name',
        'email',
        'password',
        'phone_number',
        'address',
        'identity_number',
        'status',
        'rejection_reason',
        'approved_by',
        'approved_at',
    ];

    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'approved_at' => 'datetime',
            'status' => RegistrationStatus::class,
            'password' => 'hashed',
        ];
    }

    // Relations
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', RegistrationStatus::PENDING);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', RegistrationStatus::APPROVED);
    }

    public function scopeRejected($query)
    {
        return $query->where('status', RegistrationStatus::REJECTED);
    }

    // Helper methods
    public function isPending(): bool
    {
        return $this->status === RegistrationStatus::PENDING;
    }

    public function isApproved(): bool
    {
        return $this->status === RegistrationStatus::APPROVED;
    }

    public function isRejected(): bool
    {
        return $this->status === RegistrationStatus::REJECTED;
    }
}
