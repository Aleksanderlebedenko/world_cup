<?php

namespace App\Enum;

enum WinnerEnum: string
{
    case HOME = 'home';
    case AWAY = 'away';
    case DRAW = 'draw';
    case UPCOMING = 'upcoming';
}