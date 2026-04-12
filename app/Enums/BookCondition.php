<?php

namespace App\Enums;

enum BookCondition: string
{
    case NEW = 'new';
    case GOOD = 'good';
    case FAIR = 'fair';
    case MINOR_DAMAGE = 'minor_damage';
    case MAJOR_DAMAGE = 'major_damage';
    case LOST = 'lost';

    public function label(): string
    {
        return match($this) {
            self::NEW => 'Baru',
            self::GOOD => 'Baik',
            self::FAIR => 'Cukup',
            self::MINOR_DAMAGE => 'Rusak Ringan',
            self::MAJOR_DAMAGE => 'Rusak Berat',
            self::LOST => 'Hilang',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::NEW => 'success',
            self::GOOD => 'success',
            self::FAIR => 'warning',
            self::MINOR_DAMAGE => 'warning',
            self::MAJOR_DAMAGE => 'danger',
            self::LOST => 'danger',
        };
    }
}
