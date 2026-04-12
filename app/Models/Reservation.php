<?php

namespace App\Models;

use App\Enums\ReservationStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reservation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'member_id',
        'book_id',
        'reservation_date',
        'expire_at',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'reservation_date' => 'datetime',
            'expire_at' => 'datetime',
            'status' => ReservationStatus::class,
        ];
    }

    // Relations
    public function member(): BelongsTo
    {
        return $this->belongsTo(MemberProfile::class, 'member_id');
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->whereIn('status', [ReservationStatus::WAITING, ReservationStatus::READY]);
    }

    public function scopeWaiting($query)
    {
        return $query->where('status', ReservationStatus::WAITING);
    }

    // Helper methods
    public function isActive(): bool
    {
        return in_array($this->status, [ReservationStatus::WAITING, ReservationStatus::READY]);
    }

    public function isExpired(): bool
    {
        return $this->expire_at && $this->expire_at->isPast();
    }
}
