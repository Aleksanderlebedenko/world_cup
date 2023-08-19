<?php

namespace App\DTO\Event;

use App\DTO\Pair\PairDTO;
use App\Enum\EventTypeEnum;

readonly class EventDTO
{
    public function __construct(
        public EventTypeEnum $type,
        public int $time,
        public PairDTO $pair,
    ) {
    }
}