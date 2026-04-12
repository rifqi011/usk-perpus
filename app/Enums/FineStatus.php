<?php

namespace App\Enums;

enum FineStatus: string
{
    case UNPAID = 'unpaid';
    case PAID = 'paid';
    case WAIVED = 'waived';

    public function label(): string
    {
        return match($this) {
            self::UNPAID => 'Belum Dibayar',
            self::PAID => 'Dibayar',
            self::WAIVED => 'Dibebaskan',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::UNPAID => 'danger',
            self::PAID => 'success',
            self::WAIVED => 'gray',
        };
    }
}
