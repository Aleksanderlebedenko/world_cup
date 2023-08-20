<?php

namespace App\Service;

use DateTimeImmutable;

class TimeProvider
{
    public function getCurrentTime(): DateTimeImmutable
    {
        return new DateTimeImmutable('now');
    }
}