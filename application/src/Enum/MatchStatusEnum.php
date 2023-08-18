<?php

namespace App\Enum;

enum MatchStatusEnum: string
{
    case UPCOMING = 'upcoming';
    case IN_PROGRESS = 'in_progress';
    case FINISHED = 'finished';
}