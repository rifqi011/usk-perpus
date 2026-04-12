<?php

namespace App\Enums;

enum LoanStatus: string
{
    case BORROWED = 'borrowed';
    case RETURNED = 'returned';
    case PARTIALLY_RETURNED = 'partially_returned';
    case OVERDUE = 'overdue';
    case LOST = 'lost';

    public function label(): string
    {
        return match($this) {
            self::BORROWED => 'Dipinjam',
            self::RETURNED => 'Dikembalikan',
            self::PARTIALLY_RETURNED => 'Sebagian Dikembalikan',
            self::OVERDUE => 'Terlambat',
            self::LOST => 'Hilang',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::BORROWED => 'info',
            self::RETURNED => 'success',
            self::PARTIALLY_RETURNED => 'warning',
            self::OVERDUE => 'danger',
            self::LOST => 'danger',
        };
    }
}
