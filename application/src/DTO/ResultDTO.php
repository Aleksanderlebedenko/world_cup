<?php

namespace App\DTO;

use App\Enum\WinnerEnum;

readonly class ResultDTO
{
    public function __construct(
        public int $home,
        public int $away,
        public WinnerEnum $winner,
    ) {
    }
}