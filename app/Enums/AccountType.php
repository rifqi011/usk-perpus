<?php

namespace App\Enums;

enum AccountType: string
{
    case ADMIN = 'admin';
    case MEMBER = 'member';

    public function label(): string
    {
        return match($this) {
            self::ADMIN => 'Admin',
            self::MEMBER => 'Member',
        };
    }
}
