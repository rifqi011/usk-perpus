<?php

namespace App\Models;

use App\Enums\LoanStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Loan extends Model
{
    use HasFactory;

    protected $fillable = [
        'loan_code',
        'member_id',
        'loan_rule_id',
        'loan_date',
        'due_date',
        'return_date',
        'status',
        'total_fine',
        'total_late_days',
        'notes',
        'created_by',
        'approved_by',
        'returned_by',
        'return_processed_at',
        'return_notes',
    ];

    protected function casts(): array
    {
        return [
            'loan_date' => 'date',
            'due_date' => 'date',
            'return_date' => 'date',
            'return_processed_at' => 'datetime',
            'total_fine' => 'decimal:2',
            'total_late_days' => 'integer',
            'status' => LoanStatus::class,
        ];
    }

    // Relations
    public function member(): BelongsTo
    {
        return $this->belongsTo(MemberProfile::class, 'member_id');
    }

    public function loanRule(): BelongsTo
    {
        return $this->belongsTo(LoanRule::class);
    }

    public function details(): HasMany
    {
        return $this->hasMany(LoanDetail::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function returner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'returned_by');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->whereIn('status', [LoanStatus::BORROWED, LoanStatus::OVERDUE, LoanStatus::PARTIALLY_RETURNED]);
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', LoanStatus::OVERDUE)
            ->orWhere(function ($q) {
                $q->where('status', LoanStatus::BORROWED)
                  ->where('due_date', '<', now());
            });
    }

    // Helper methods
    public function isActive(): bool
    {
        return in_array($this->status, [LoanStatus::BORROWED, LoanStatus::OVERDUE, LoanStatus::PARTIALLY_RETURNED]);
    }

    public function isOverdue(): bool
    {
        return $this->status === LoanStatus::OVERDUE || 
               ($this->status === LoanStatus::BORROWED && $this->due_date->isPast());
    }

    public function calculateLateDays(): int
    {
        if (!$this->isOverdue()) {
            return 0;
        }

        $returnDate = $this->return_date ?? now();
        return max(0, $this->due_date->diffInDays($returnDate, false));
    }
}
