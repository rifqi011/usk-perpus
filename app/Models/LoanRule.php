<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class LoanRule extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'max_active_loans',
        'max_loan_days',
        'fine_per_day',
        'grace_days',
        'can_renew',
        'max_renew_count',
        'damage_fine_minor',
        'damage_fine_major',
        'lost_book_fine_type',
        'lost_book_fine_amount',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'max_active_loans' => 'integer',
            'max_loan_days' => 'integer',
            'fine_per_day' => 'decimal:2',
            'grace_days' => 'integer',
            'can_renew' => 'boolean',
            'max_renew_count' => 'integer',
            'damage_fine_minor' => 'decimal:2',
            'damage_fine_major' => 'decimal:2',
            'lost_book_fine_amount' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    // Relations
    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Helper methods
    public function calculateDueDate(\DateTime $loanDate): \DateTime
    {
        return (clone $loanDate)->modify("+{$this->max_loan_days} days");
    }

    public function calculateLateFine(int $lateDays): float
    {
        $effectiveLateDays = max(0, $lateDays - $this->grace_days);
        return $effectiveLateDays * $this->fine_per_day;
    }
}
