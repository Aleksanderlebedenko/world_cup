<?php

namespace App\DTO\Event;

readonly class EventsDTO
{
    /**
     * @param EventDTO[] $events
     */
    public function __construct(
        public array $events = [],
    ) {
    }
}