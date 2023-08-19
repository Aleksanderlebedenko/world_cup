<?php

namespace App\Service;

use App\DTO\Event\EventsDTO;
use App\Exception\CannotGetEventsException;
use App\Service\Event\EventsProvider;
use Psr\Log\LoggerInterface;

readonly class EventService
{
    public function __construct(
        private EventsProvider $eventsProviderRandomizer,
        private LoggerInterface $logger,
    ) {
    }

    public function getEvents(): EventsDTO
    {
        try {
            return $this->eventsProviderRandomizer->getEvents();
        } catch (CannotGetEventsException $exception) {
            $this->logger->error($exception->getMessage());
            return new EventsDTO([]);
        }
    }
}