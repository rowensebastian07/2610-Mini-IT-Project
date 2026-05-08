<?php

namespace App\Enums;

enum UserVerification: string
{
    case VERIFIED    = 'verified';
    case PENDING     = 'pending';
    case UNVERIFIED  = 'unverified';

    public function label(): string
    {
        return match ($this) {
            self::VERIFIED   => 'Verified',
            self::PENDING    => 'Pending',
            self::UNVERIFIED => 'Unverified',
        };
    }
}
