<?php

namespace App\DTO;

use App\Enum\WinnerEnum;

readonly class ResultDTO
{
    public function __construct(
        public readonly int $home,
        public readonly int $away,
        public readonly WinnerEnum $winner,
    ) {
    }
}