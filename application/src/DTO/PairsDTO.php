<?php

namespace App\DTO;

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