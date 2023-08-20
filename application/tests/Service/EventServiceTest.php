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
use App\Exception\CannotGetEventsException;
use App\Service\Event\EventPairsSynchronizer;
use App\Service\Event\EventsProvider;
use App\Service\EventService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class EventServiceTest extends TestCase
{
    private EventService $eventService;
    private EventsProvider|MockObject $eventsProvider;
    private MockObject|LoggerInterface $logger;
    private MockObject|EventPairsSynchronizer $eventPairsSynchronizer;

    protected function setUp(): void
    {
        $this->eventsProvider = $this->createMock(EventsProvider::class);
        $this->eventPairsSynchronizer = $this->createMock(EventPairsSynchronizer::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        $this->eventService = new EventService(
            $this->eventsProvider,
            $this->eventPairsSynchronizer,
            $this->logger,
        );
    }

    public function testGetEvents()
    {
        $this->eventsProvider->method('getEvents')->willReturn(new EventsDTO([$this->getEventDTO()]));
        $this->eventPairsSynchronizer->expects($this->once())->method('sync');
        $result = $this->eventService->getEvents();

        $this->assertEquals(new EventsDTO([$this->getEventDTO()]), $result);
    }

    public function testGetEventsThrowsException()
    {
        $this->eventsProvider->method('getEvents')->willThrowException(new CannotGetEventsException());
        $this->logger->expects($this->once())->method('error');
        $this->eventPairsSynchronizer->expects($this->never())->method('sync');


        $result = $this->eventService->getEvents();

        $this->assertEquals(new EventsDTO([]), $result);
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
