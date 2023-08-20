<?php

namespace App\Service\Pair;

use App\DTO\Pair\PairsDTO;
use App\Enum\MatchStatusEnum;
use App\Exception\CannotGetPairsException;
use Psr\Cache\InvalidArgumentException;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class PairsProviderFromCache implements PairsProvider
{
    private const PAIRS_CACHE_KEY = 'pairs';
    private const PAIRS_CACHE_EXPIRES_PERIOD = '1 day';

    public function __construct(
        private readonly CacheInterface $cache,
        private readonly PairsStorage $pairsStorage,
    ) {
    }

    public function getUpcomingPairs(): PairsDTO
    {
        $pairs = $this->getPairs();

        return $this->getPairsByStatus($pairs, MatchStatusEnum::UPCOMING);
    }

    public function getCurrentPairs(): PairsDTO
    {
        $pairs = $this->getPairs();

        return $this->getPairsByStatus($pairs, MatchStatusEnum::IN_PROGRESS);
    }

    public function getFinishedPairs(): PairsDTO
    {
        $pairs = $this->getPairs();

        return $this->getPairsByStatus($pairs, MatchStatusEnum::FINISHED);
    }

    public function getPairs(): PairsDTO
    {
        try {
            return $this->cache->get(
                self::PAIRS_CACHE_KEY,
                function (ItemInterface $item) {
                    $item->expiresAfter(
                        date_interval_create_from_date_string(self::PAIRS_CACHE_EXPIRES_PERIOD)
                    );

                    return $this->pairsStorage->getPairs();
                }
            );
        } catch (InvalidArgumentException $e) {
            throw new CannotGetPairsException(
                sprintf('Cannot get pairs from cache: %s', $e->getMessage()),
                $e->getCode(),
                $e
            );
        }
    }

    public function resetPairs(PairsDTO $pairsDTO): PairsDTO
    {
        try {
            $this->cache->delete(self::PAIRS_CACHE_KEY);

            return $this->cache->get(
                self::PAIRS_CACHE_KEY,
                function (ItemInterface $item) use ($pairsDTO) {
                    $item->expiresAfter(
                        date_interval_create_from_date_string(self::PAIRS_CACHE_EXPIRES_PERIOD)
                    );

                    return $pairsDTO;
                }
            );
        } catch (InvalidArgumentException $e) {
            throw new CannotGetPairsException(
                sprintf('Cache storage error: %s', $e->getMessage()),
                $e->getCode(),
                $e
            );
        }
    }

    private function getPairsByStatus(PairsDTO $pairs, MatchStatusEnum $matchStatusEnum): PairsDTO
    {
        $sortedPairs = [];
        foreach ($pairs->pairs as $pair) {
            if ($pair->status === $matchStatusEnum) {
                $sortedPairs[] = $pair;
            }
        }

        return new PairsDTO($sortedPairs);
    }
}