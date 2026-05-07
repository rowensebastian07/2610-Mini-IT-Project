<?php

namespace App\Enums;

enum ClubCategory: string
{
    case ARTS = 'Arts Clubs';
    case COMMUNITY = 'Community Clubs';
    case RELIGIOUS = 'Religious Clubs';
    case GAMES_ENTERTAINMENT = 'Games / Entertainment Clubs';
    case CULTURAL = 'Cultural Clubs';
    case TECH = 'Tech Clubs';
    case RECREATIONAL_PHYSICAL = 'Recreational / Physical Activities Clubs';

    public function label(): string
    {
        return match($this) {
            self::ARTS => 'Arts Clubs',
            self::COMMUNITY => 'Community Clubs',
            self::RELIGIOUS => 'Religious Clubs',
            self::GAMES_ENTERTAINMENT => 'Games / Entertainment Clubs',
            self::CULTURAL => 'Cultural Clubs',
            self::TECH => 'Tech Clubs',
            self::RECREATIONAL_PHYSICAL => 'Recreational / Physical Activities Clubs',
        };
    }
}
