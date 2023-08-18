<?php

namespace App\Service\Pairs;

use App\DTO\PairsDTO;
use App\Enum\MatchStatusEnum;

interface PairsProvider
{
    public function getUpcomingPairs(): PairsDTO;
    public function getCurrentPairs(): PairsDTO;
    public function getFinishedPairs(): PairsDTO;
}