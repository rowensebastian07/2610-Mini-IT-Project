<?php

namespace App\Enums;

enum ClubCategory: string
{
    case ART = 'Arts Clubs';
    case STUDENT = 'Community Clubs';
    case SPORTS = 'Recreational / Physical Activities Clubs';
    case RELIGION = 'Religious Clubs';
    case CULTURE = 'Cultural Clubs';
    case GAMES = 'Games / Entertainment Clubs';
    case TECH = 'Tech Clubs';

    public function label(): string
    {
        return match($this) {
            self::ART => 'Arts Clubs',
            self::STUDENT => 'Community Clubs',
            self::SPORTS => 'Recreational / Physical Activities Clubs',
            self::RELIGION => 'Religious Clubs',
            self::CULTURE => 'Cultural Clubs',
            self::GAMES => 'Games / Entertainment Clubs',
            self::TECH => 'Tech Clubs'
        };
    }
}