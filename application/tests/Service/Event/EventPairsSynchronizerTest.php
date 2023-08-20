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
use App\Service\Event\EventPairsSynchronizer;
use App\Service\Pair\PairsProviderFromCache;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class EventPairsSynchronizerTest extends TestCase
{
    private EventPairsSynchronizer $eventPairsSynchronizer;
    private MockObject|PairsProviderFromCache $pairsProvider;

    protected function setUp(): void
    {
        $this->pairsProvider = $this->createMock(PairsProviderFromCache::class);
        $this->eventPairsSynchronizer = new EventPairsSynchronizer(
            $this->pairsProvider
        );
    }

    public function testSync(): void
    {
        $events = $this->getEventsDTO();
        $pairs = $this->getInitialPairsDTO();

        $this->pairsProvider->method('getPairs')->willReturn($pairs);
        $this->pairsProvider->expects($this->once())
            ->method('resetPairs')
            ->with($this->getFinalPairsDTO());

        $this->eventPairsSynchronizer->sync($events);
    }

    private function getEventsDTO(): EventsDTO
    {
        return new EventsDTO([
                new EventDTO(
                    EventTypeEnum::GOAL,
                    0,
                    $this->getFinalPairDTO(),
                ),
            ]
        );
    }

    function getInitialPairsDTO(): PairsDTO
    {
        return new PairsDTO([
            new PairDTO(
                1,
                CountryTeamEnum::ARGENTINA,
                CountryTeamEnum::BRAZIL,
                MatchStatusEnum::IN_PROGRESS,
                new ResultDTO(
                    0,
                    0,
                    WinnerEnum::HOME,
                ),
            ),
            new PairDTO(
                2,
                CountryTeamEnum::GERMANY,
                CountryTeamEnum::NORWAY,
                MatchStatusEnum::UPCOMING,
                new ResultDTO(
                    0,
                    0,
                    WinnerEnum::UPCOMING,
                ),
            ),
        ]);
    }

    function getFinalPairsDTO(): PairsDTO
    {
        return new PairsDTO([
            $this->getFinalPairDTO(),
            new PairDTO(
                2,
                CountryTeamEnum::GERMANY,
                CountryTeamEnum::NORWAY,
                MatchStatusEnum::UPCOMING,
                new ResultDTO(
                    0,
                    0,
                    WinnerEnum::UPCOMING,
                ),
            ),
        ]);
    }

    private function getFinalPairDTO(): PairDTO
    {
        return new PairDTO(
            1,
            CountryTeamEnum::ARGENTINA,
            CountryTeamEnum::BRAZIL,
            MatchStatusEnum::IN_PROGRESS,
            new ResultDTO(
                1,
                0,
                WinnerEnum::HOME,
            ),
        );
    }
}
