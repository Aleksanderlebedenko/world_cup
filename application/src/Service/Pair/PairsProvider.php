<?php

namespace App\Service\Pair;

use App\DTO\Pair\PairsDTO;

interface PairsProvider
{
    public function getUpcomingPairs(): PairsDTO;
    public function getCurrentPairs(): PairsDTO;
    public function getFinishedPairs(): PairsDTO;
}