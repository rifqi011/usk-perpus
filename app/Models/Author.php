<?php

namespace App\Models;

use App\Enums\ActiveStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Author extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'photo',
        'about',
        'nationality',
        'email',
        'phone',
        'website',
        'facebook',
        'twitter',
        'instagram',
        'linkedin',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => ActiveStatus::class,
        ];
    }

    // Relations
    public function books(): BelongsToMany
    {
        return $this->belongsToMany(Book::class, 'book_authors')->withTimestamps();
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', ActiveStatus::ACTIVE);
    }
}
