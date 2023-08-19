<?php

namespace App\DTO;

use App\Enum\WinnerEnum;

class ResultDTO
{
    public function __construct(
        public int $home,
        public int $away,
        public WinnerEnum $winner,
    ) {
    }
}