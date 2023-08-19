<?php

namespace App\DTO\Pair;

class PairsDTO
{
    /**
     * @param PairDTO[] $pairs
     */
    public function __construct(
        public array $pairs = [],
    ) {
    }
}