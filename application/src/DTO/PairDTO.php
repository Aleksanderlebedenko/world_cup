<?php

namespace App\DTO;

use App\Enum\CountryTeamEnum;
use App\Enum\MatchStatusEnum;
use DateTimeImmutable;

readonly class PairDTO
{
    public function __construct(
        public string $id,
        public CountryTeamEnum $homeTeam,
        public CountryTeamEnum $awayTeam,
        public MatchStatusEnum $status,
        public ResultDTO $result,
        public ?DateTimeImmutable $startDate = null,
        public ?DateTimeImmutable $endDate = null,
    ) {
    }
}