<?php

namespace App\Service\Event;

use App\DTO\Event\EventsDTO;
use App\Service\Pair\PairsProviderFromCache;

class EventPairsSynchronizer
{
    public function __construct(
        private PairsProviderFromCache $pairsProviderFromCache,
    ) {
    }

    public function sync(EventsDTO $eventsDTO): void
    {
        $pairsDTO = $this->pairsProviderFromCache->getPairs();
        foreach ($pairsDTO->pairs as $key => $pairDTO) {
            foreach ($eventsDTO->events as $eventDTO) {
                if ($eventDTO->pair->id === $pairDTO->id) {
                    $pairsDTO->pairs[$key] = $eventDTO->pair;
                }
            }
        }

        $this->pairsProviderFromCache->resetPairs($pairsDTO);
    }
}