<?php

namespace App\Service\Provider;

use App\DTO\Event\EventsDTO;

readonly class EventPairsSynchronizer
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