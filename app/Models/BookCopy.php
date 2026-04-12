<?php

namespace App\Models;

use App\Enums\BookCondition;
use App\Enums\CopyStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class BookCopy extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'book_id',
        'barcode',
        'inventory_code',
        'acquisition_date',
        'source',
        'price',
        'condition',
        'copy_status',
        'last_borrowed_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'acquisition_date' => 'date',
            'price' => 'decimal:2',
            'last_borrowed_at' => 'datetime',
            'condition' => BookCondition::class,
            'copy_status' => CopyStatus::class,
        ];
    }

    // Relations
    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function loanDetails(): HasMany
    {
        return $this->hasMany(LoanDetail::class);
    }

    // Scopes
    public function scopeAvailable($query)
    {
        return $query->where('copy_status', CopyStatus::AVAILABLE);
    }

    public function scopeBorrowed($query)
    {
        return $query->where('copy_status', CopyStatus::BORROWED);
    }

    // Helper methods
    public function isAvailable(): bool
    {
        return $this->copy_status === CopyStatus::AVAILABLE;
    }

    public function markAsBorrowed(): void
    {
        $this->update([
            'copy_status' => CopyStatus::BORROWED,
            'last_borrowed_at' => now(),
        ]);
    }

    public function markAsAvailable(): void
    {
        $this->update(['copy_status' => CopyStatus::AVAILABLE]);
    }
}
