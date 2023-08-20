<?php

namespace App\Service;

use App\DTO\Event\EventsDTO;
use App\Exception\CannotGetEventsException;
use App\Service\Event\EventPairsSynchronizer;
use App\Service\Event\EventsProvider;
use Psr\Log\LoggerInterface;

/**
 * In the real life $eventPairsSynchronizer is not needed, because synchronization of pairs state and upcoming events
 * should be triggered not from this place. But from data consistency perspective, I've put it here,
 * just for making it work.
 * But I understand that this violates SRP and CQS principles.
 */
readonly class EventService
{
    public function __construct(
        private EventsProvider $eventsProviderRandomizer,
        private EventPairsSynchronizer $eventPairsSynchronizer,
        private LoggerInterface $logger,
    ) {
    }

    public function getEvents(): EventsDTO
    {
        try {
            $eventsDTO = $this->eventsProviderRandomizer->getEvents();

            $this->eventPairsSynchronizer->sync($eventsDTO);

            return $eventsDTO;
        } catch (CannotGetEventsException $exception) {
            $this->logger->error($exception->getMessage());
            return new EventsDTO([]);
        }
    }
}