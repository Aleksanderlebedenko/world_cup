<?php

namespace App\DTO;

use App\Enum\CountryTeamEnum;
use App\Enum\MatchStatusEnum;
use DateTimeImmutable;

readonly class PairDTO
{
    public function __construct(
        public readonly CountryTeamEnum $homeTeam,
        public readonly CountryTeamEnum $awayTeam,
        public readonly MatchStatusEnum $status,
        public readonly ResultDTO $result,
        public readonly ?DateTimeImmutable $startDate = null,
        public readonly ?DateTimeImmutable $endDate = null,
    ) {
    }
}