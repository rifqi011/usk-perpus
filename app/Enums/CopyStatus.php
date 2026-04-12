<?php

namespace App\Enums;

enum CopyStatus: string
{
    case AVAILABLE = 'available';
    case BORROWED = 'borrowed';
    case RESERVED = 'reserved';
    case MAINTENANCE = 'maintenance';
    case LOST = 'lost';
    case DISCARDED = 'discarded';

    public function label(): string
    {
        return match($this) {
            self::AVAILABLE => 'Tersedia',
            self::BORROWED => 'Dipinjam',
            self::RESERVED => 'Direservasi',
            self::MAINTENANCE => 'Maintenance',
            self::LOST => 'Hilang',
            self::DISCARDED => 'Dibuang',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::AVAILABLE => 'success',
            self::BORROWED => 'info',
            self::RESERVED => 'warning',
            self::MAINTENANCE => 'warning',
            self::LOST => 'danger',
            self::DISCARDED => 'gray',
        };
    }
}
