<?php

namespace App\Enums;

enum ClubRole: string
{
    case PRESIDENT = 'president';
    case HICOM = 'high committee';
    case SUBCOM = 'sub committee';
    case MEMBER = 'member';

    public function label(): string
    {
        return match ($this) {
            self::PRESIDENT => 'President',
            self::HICOM => 'High Committee',
            self::SUBCOM => 'Sub Committee',
            self::MEMBER => 'Member',
        };
    }
}