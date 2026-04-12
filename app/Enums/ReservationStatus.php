<?php

namespace App\Enums;

enum ReservationStatus: string
{
    case WAITING = 'waiting';
    case READY = 'ready';
    case CANCELLED = 'cancelled';
    case FULFILLED = 'fulfilled';
    case EXPIRED = 'expired';

    public function label(): string
    {
        return match($this) {
            self::WAITING => 'Menunggu',
            self::READY => 'Siap',
            self::CANCELLED => 'Dibatalkan',
            self::FULFILLED => 'Terpenuhi',
            self::EXPIRED => 'Kadaluarsa',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::WAITING => 'warning',
            self::READY => 'success',
            self::CANCELLED => 'gray',
            self::FULFILLED => 'success',
            self::EXPIRED => 'danger',
        };
    }
}
