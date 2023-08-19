<?php

namespace App\Service;

use App\DTO\Pair\PairsDTO;
use App\Exception\CannotGetPairsException;
use App\Service\Pairs\PairsProvider;
use Psr\Log\LoggerInterface;

readonly class ScoreboardService
{
    public function __construct(
        private PairsProvider $pairsProviderFromCache,
        private LoggerInterface $logger,
    ) {
    }
    public function getUpcomingPairs(): PairsDTO
    {
        try {
            return $this->pairsProviderFromCache->getUpcomingPairs();
        } catch (CannotGetPairsException $e) {
            $this->logger->error($e->getMessage());
            return new PairsDTO([]);
        }
    }

    public function getCurrentPairs(): PairsDTO
    {
        try {
            return $this->pairsProviderFromCache->getCurrentPairs();
        } catch (CannotGetPairsException $e) {
            $this->logger->error($e->getMessage());
            return new PairsDTO([]);
        }
    }

    public function getFinishedPairs(): PairsDTO
    {
        try {
            return $this->pairsProviderFromCache->getFinishedPairs();
        } catch (CannotGetPairsException $e) {
            $this->logger->error($e->getMessage());
            return new PairsDTO([]);
        }
    }
}