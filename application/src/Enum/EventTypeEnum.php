<?php

namespace App\Enum;

enum EventTypeEnum: string
{
    case START = 'start';
    case END = 'end';
    case GOAL = 'goal';
}