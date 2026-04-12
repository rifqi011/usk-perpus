<?php

namespace App\Models;

use App\Enums\ActiveStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Book extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id',
        'publisher_id',
        'shelf_id',
        'title',
        'slug',
        'isbn',
        'sku',
        'year',
        'edition',
        'language',
        'page_count',
        'description',
        'synopsis',
        'cover_image',
        'purchase_price',
        'replacement_price',
        'initial_stock',
        'stock',
        'available_stock',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'year' => 'integer',
            'page_count' => 'integer',
            'purchase_price' => 'decimal:2',
            'replacement_price' => 'decimal:2',
            'initial_stock' => 'integer',
            'stock' => 'integer',
            'available_stock' => 'integer',
            'status' => ActiveStatus::class,
        ];
    }

    // Relations
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function publisher(): BelongsTo
    {
        return $this->belongsTo(Publisher::class);
    }

    public function shelf(): BelongsTo
    {
        return $this->belongsTo(Shelf::class);
    }

    public function authors(): BelongsToMany
    {
        return $this->belongsToMany(Author::class, 'book_authors')->withTimestamps();
    }

    public function genres(): BelongsToMany
    {
        return $this->belongsToMany(Genre::class, 'book_genres')->withTimestamps();
    }

    public function copies(): HasMany
    {
        return $this->hasMany(BookCopy::class);
    }

    public function loanDetails(): HasMany
    {
        return $this->hasMany(LoanDetail::class);
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', ActiveStatus::ACTIVE);
    }

    public function scopeAvailable($query)
    {
        return $query->where('available_stock', '>', 0);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('isbn', 'like', "%{$search}%")
              ->orWhere('sku', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%");
        });
    }

    // Helper methods
    public function isAvailable(): bool
    {
        return $this->available_stock > 0;
    }

    public function updateStock(): void
    {
        $borrowed = $this->loanDetails()
            ->where('status', 'borrowed')
            ->count();
        $this->available_stock = max(0, $this->stock - $borrowed);
        $this->save();
    }
}
