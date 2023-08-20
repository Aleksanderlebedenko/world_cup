<?php

namespace App\Service;

class GetRandomNumberService
{
    public function getRandomNumber(int $from, int $to): int
    {
        return rand($from, $to);
    }
}