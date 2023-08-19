<?php

namespace App\DTO\Pair;

readonly class PairsDTO
{
    /**
     * @param PairDTO[] $pairs
     */
    public function __construct(
        public array $pairs = [],
    ) {
    }
}