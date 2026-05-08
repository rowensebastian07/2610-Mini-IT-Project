<?php

namespace App\Enums;

enum UserStatus: string
{
    case ACTIVE = 'active';
    case PENDING  = 'pending';
    case BANNED  = 'banned';

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => 'Active User',
            self::PENDING  => 'Pending Approval',
            self::BANNED  => 'Account Banned',
        };
    }
}
