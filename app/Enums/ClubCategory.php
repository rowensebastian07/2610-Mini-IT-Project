<?php

namespace App\Enums;

enum ClubCategory: string
{
    case ART = 'Arts Clubs';
    case COMMUNITY = 'Community Clubs';
    case RELIGIOUS = 'Religious Clubs';
    case ENTERTAINMENT = 'Games / Entertainment Clubs';
    case CULTURAL = 'Cultural Clubs';
    case TECH = 'Tech Clubs';
    case RECREATIONAL = 'Recreational / Physical Activities Clubs';

    public function label(): string
    {
        return match($this) {
            self::ART => 'Arts Clubs',
            self::COMMUNITY => 'Community Clubs',
            self::RELIGIOUS => 'Religious Clubs',
            self::ENTERTAINMENT => 'Games / Entertainment Clubs',
            self::CULTURAL => 'Cultural Clubs',
            self::TECH => 'Tech Clubs',
            self::RECREATIONAL => 'Recreational / Physical Activities Clubs',
        };
    }
}
