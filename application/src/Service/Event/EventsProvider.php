<?php

namespace App\Service\Event;

use App\DTO\Event\EventsDTO;

interface EventsProvider
{
    public function getEvents(): EventsDTO;
}