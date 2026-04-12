<?php

namespace App\Models;

use App\Enums\LoanStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LoanDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'loan_id',
        'book_id',
        'due_date',
        'returned_at',
        'late_days',
        'fine_amount',
        'status',
        'renewed_count',
        'returned_by',
        'return_notes',
        'fine_generated',
    ];

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
            'returned_at' => 'datetime',
            'late_days' => 'integer',
            'fine_amount' => 'decimal:2',
            'renewed_count' => 'integer',
            'fine_generated' => 'boolean',
            'status' => LoanStatus::class,
        ];
    }

    // Relations
    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loan::class);
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function returner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'returned_by');
    }

    public function fines(): HasMany
    {
        return $this->hasMany(Fine::class);
    }

    // Helper methods
    public function isReturned(): bool
    {
        return $this->status === LoanStatus::RETURNED;
    }

    public function isOverdue(): bool
    {
        return !$this->isReturned() && $this->due_date->isPast();
    }
}
