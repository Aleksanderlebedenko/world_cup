<?php

namespace tests\Service;

use App\DTO\Event\EventDTO;
use App\DTO\Event\EventsDTO;
use App\DTO\Pair\PairDTO;
use App\DTO\ResultDTO;
use App\Enum\CountryTeamEnum;
use App\Enum\EventTypeEnum;
use App\Enum\MatchStatusEnum;
use App\Enum\WinnerEnum;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class EventServiceTest extends TestCase
{
    private EventService $eventService;
    private EventProvider|MockObject $eventProvider;

    protected function setUp(): void
    {
        $this->eventProvider = $this->createMock(EventProvider::class);
        $this->eventService = new EventService($this->eventProvider);
    }

    public function testGetEvents()
    {
        $this->eventProvider->method('getEvents')->willReturn(new EventsDTO([$this->getEventDTO()]));
        $result = $this->eventService->getEvents();

        $this->assertEquals(new EventsDTO([$this->getEventDTO()]), $result);
    }

    private function getEventDTO(): EventDTO
    {
        return new EventDTO(
            EventTypeEnum::START,
            0,
            new PairDTO(
                1,
                CountryTeamEnum::CZECH_REPUBLIC,
                CountryTeamEnum::DENMARK,
                MatchStatusEnum::IN_PROGRESS,
                new ResultDTO(
                    0,
                    0,
                    WinnerEnum::DRAW,
                ),
            ),
        );
    }
}
