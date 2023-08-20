<?php

namespace tests\Service;

use App\DTO\Event\EventDTO;
use App\DTO\Event\EventsDTO;
use App\DTO\Pair\PairDTO;
use App\DTO\Pair\PairsDTO;
use App\DTO\ResultDTO;
use App\Enum\CountryTeamEnum;
use App\Enum\EventTypeEnum;
use App\Enum\MatchStatusEnum;
use App\Enum\WinnerEnum;
use App\Service\Event\RandomEventsProvider;
use App\Service\GetRandomNumberService;
use App\Service\Pair\PairsProvider;
use App\Service\TimeProvider;
use DateTimeImmutable;
use Generator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class PairsProviderFromCacheTest extends TestCase
{
    private PairsProvider|MockObject $pairsProvider;
    private RandomEventsProvider $randomEventsProvider;
    private MockObject|GetRandomNumberService $getRandomNumberService;
    private DateTimeImmutable $currentTime;
    private TimeProvider|MockObject $timeProvider;

    protected function setUp(): void
    {
        $this->provideNowDate();

        $this->pairsProvider = $this->createMock(PairsProvider::class);
        $this->getRandomNumberService = $this->createMock(GetRandomNumberService::class);
        $this->timeProvider = $this->createMock(TimeProvider::class);

        $this->randomEventsProvider = new RandomEventsProvider(
            $this->pairsProvider,
            $this->getRandomNumberService,
            $this->timeProvider,
        );
    }

    /**
     * @dataProvider pairsProvider
     * @param PairDTO[] $upcomingPairs
     * @param PairDTO[] $currentPairs
     */
    public function testGetEvents(array $upcomingPairs, array $currentPairs, EventsDTO $expected): void
    {
        $this->pairsProvider->method('getUpcomingPairs')->willReturn(new PairsDTO($upcomingPairs));
        $this->pairsProvider->method('getCurrentPairs')->willReturn(new PairsDTO($currentPairs));
        $this->timeProvider->expects($this->any())->method('getCurrentTime')->willReturn($this->provideNowDate());
        $this->getRandomNumberService->expects($this->any())->method('getRandomNumber')->willReturn(1);

        $result = $this->randomEventsProvider->getEvents();

        $this->assertEquals($expected, $result);
    }

    public function pairsProvider(): Generator
    {
        yield [
            'upcomingPairs' => $this->getUpcomingPairs(),
            'currentPairs' => $this->getCurrentPairs(),
            'expectedEvents' => $this->buildExpectedEvents($this->getUpcomingPairs(), $this->getCurrentPairs()),
        ];
        yield [
            'upcomingPairs' => [],
            'currentPairs' => [],
            'expectedEvents' => new EventsDTO([]),

        ];
        yield [
            'upcomingPairs' => $this->getUpcomingPairs(),
            'currentPairs' => [],
            'expectedEvents' => $this->buildExpectedEvents($this->getUpcomingPairs(), []),
        ];
        yield [
            'upcomingPairs' => [],
            'currentPairs' => $this->getCurrentPairs(),
            'expectedEvents' => $this->buildExpectedEvents([], $this->getCurrentPairs()),
        ];
    }

    /**
     * @return PairDTO[]
     */
    private function getUpcomingPairs(): array
    {
        return [
            new PairDTO(
                id: 1,
                homeTeam: CountryTeamEnum::ENGLAND,
                awayTeam: CountryTeamEnum::CROATIA,
                status: MatchStatusEnum::UPCOMING,
                result: new ResultDTO(0, 0, WinnerEnum::UPCOMING),
                startDate: $this->provideNowDate(),
            ),
        ];
    }

    /**
     * @return PairDTO[]
     */
    private function getCurrentPairs(): array
    {
        return [
            new PairDTO(
                id: 3,
                homeTeam: CountryTeamEnum::AUSTRIA,
                awayTeam: CountryTeamEnum::HUNGARY,
                status: MatchStatusEnum::IN_PROGRESS,
                result: new ResultDTO(0, 0, WinnerEnum::DRAW),
                startDate: $this->provideNowDate(),
            ),
        ];
    }

    /**
     * @param PairDTO[] $upcomingPairs
     * @param PairDTO[] $currentPairs
     */
    private function buildExpectedEvents(array $upcomingPairs, array $currentPairs): EventsDTO
    {
        $events = [];
        foreach ($upcomingPairs as $pair) {
            $events[] = $this->buildStartEvent($pair);
        }
        foreach ($currentPairs as $pair) {
            $events[] = $this->buildGoalEvent($pair);
        }

        return new EventsDTO($events);
    }

    private function buildStartEvent(PairDTO $pair): EventDTO
    {
        $pair->status = MatchStatusEnum::IN_PROGRESS;
        $pair->result->winner = WinnerEnum::DRAW;
        $pair->startDate = $this->provideNowDate();

        return new EventDTO(
            EventTypeEnum::START,
            0,
            $pair,
        );
    }

    private function buildGoalEvent(PairDTO $pair): EventDTO
    {
        $pair->result->winner = WinnerEnum::HOME;
        $pair->result->home = 1;
        $pair->startDate = $this->provideNowDate();

        return new EventDTO(
            EventTypeEnum::GOAL,
            1,
            $pair,
        );
    }

    private function provideNowDate(): DateTimeImmutable
    {
        if (isset($this->currentTime)) {
            return $this->currentTime;
        }

        return $this->currentTime = new DateTimeImmutable(date('Y-m-d'));
    }
}
