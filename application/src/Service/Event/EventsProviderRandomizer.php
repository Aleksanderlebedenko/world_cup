<?php

namespace App\Service\Event;

use App\DTO\Event\EventsDTO;

class EventsProviderRandomizer implements EventsProvider
{
    public function getEvents(): EventsDTO
    {
        return new EventsDTO([]);
    }
}