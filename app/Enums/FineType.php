<?php

namespace App\Enums;

enum FineType: string
{
    case LATE_RETURN = 'late_return';
    case MINOR_DAMAGE = 'minor_damage';
    case MAJOR_DAMAGE = 'major_damage';
    case LOST_BOOK = 'lost_book';
    case OTHER = 'other';

    public function label(): string
    {
        return match($this) {
            self::LATE_RETURN => 'Keterlambatan',
            self::MINOR_DAMAGE => 'Kerusakan Ringan',
            self::MAJOR_DAMAGE => 'Kerusakan Berat',
            self::LOST_BOOK => 'Buku Hilang',
            self::OTHER => 'Lainnya',
        };
    }
}
