<?php

namespace App\Models;

use App\Enums\FineStatus;
use App\Enums\FineType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Fine extends Model
{
    use HasFactory;

    protected $fillable = [
        'loan_detail_id',
        'member_id',
        'fine_type',
        'calculation_type',
        'qty',
        'rate',
        'amount',
        'status',
        'paid_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'qty' => 'integer',
            'rate' => 'decimal:2',
            'amount' => 'decimal:2',
            'paid_at' => 'datetime',
            'fine_type' => FineType::class,
            'status' => FineStatus::class,
        ];
    }

    // Relations
    public function loanDetail(): BelongsTo
    {
        return $this->belongsTo(LoanDetail::class);
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(MemberProfile::class, 'member_id');
    }

    // Scopes
    public function scopeUnpaid($query)
    {
        return $query->where('status', FineStatus::UNPAID);
    }

    public function scopePaid($query)
    {
        return $query->where('status', FineStatus::PAID);
    }

    // Helper methods
    public function isPaid(): bool
    {
        return $this->status === FineStatus::PAID;
    }

    public function markAsPaid(): void
    {
        $this->update([
            'status' => FineStatus::PAID,
            'paid_at' => now(),
        ]);
    }

    public function waive(): void
    {
        $this->update(['status' => FineStatus::WAIVED]);
    }
}
