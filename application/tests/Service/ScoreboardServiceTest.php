<?php

namespace tests\Service;

use App\DTO\PairDTO;
use App\DTO\PairsDTO;
use App\DTO\ResultDTO;
use App\Enum\CountryTeamEnum;
use App\Enum\MatchStatusEnum;
use App\Enum\WinnerEnum;
use App\Exception\CannotGetPairsException;
use App\Service\Pairs\PairsProvider;
use App\Service\ScoreboardService;
use Generator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class ScoreboardServiceTest extends TestCase
{
    private ScoreboardService $scoreboardService;
    private MockObject|PairsProvider $pairsProviderFromCache;
    private MockObject|LoggerInterface $logger;

    protected function setUp(): void
    {
        $this->pairsProviderFromCache = $this->createMock(PairsProvider::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        $this->scoreboardService = new ScoreboardService(
            $this->pairsProviderFromCache,
            $this->logger,
        );
    }

    /**
     * @dataProvider upcomingPairsProvider
     */
    public function testGetUpcomingPairs(PairsDTO $providerReturns, PairsDTO $actual): void
    {
        $this->pairsProviderFromCache->method('getUpcomingPairs')->willReturn($providerReturns);

        $result = $this->scoreboardService->getUpcomingPairs();

        $this->assertEquals($actual, $result);
    }

    public function testGetUpcomingPairsThrowsException(): void
    {
        $this->pairsProviderFromCache->method('getUpcomingPairs')->willThrowException(new CannotGetPairsException());
        $this->logger->expects($this->once())->method('error');

        $result = $this->scoreboardService->getUpcomingPairs();

        $this->assertEquals(new PairsDTO([]), $result);
    }

    /**
     * @dataProvider currentPairsProvider
     */
    public function testGetCurrentPairs(PairsDTO $providerReturns, PairsDTO $actual): void
    {
        $this->pairsProviderFromCache->method('getCurrentPairs')->willReturn($providerReturns);

        $result = $this->scoreboardService->getCurrentPairs();

        $this->assertEquals($actual, $result);
    }

    public function testGetCurrentPairsThrowsException(): void
    {
        $this->pairsProviderFromCache->method('getCurrentPairs')
            ->willThrowException(new CannotGetPairsException());
        $this->logger->expects($this->once())->method('error');

        $result = $this->scoreboardService->getCurrentPairs();

        $this->assertEquals(new PairsDTO([]), $result);
    }

    /**
     * @dataProvider finishedPairsProvider
     */
    public function testGetFinishedPairs(PairsDTO $providerReturns, PairsDTO $actual): void
    {
        $this->pairsProviderFromCache->method('getFinishedPairs')->willReturn($providerReturns);

        $result = $this->scoreboardService->getFinishedPairs();

        $this->assertEquals($actual, $result);
    }

    public function testGetFinishedPairsThrowsException(): void
    {
        $this->pairsProviderFromCache->method('getFinishedPairs')
            ->willThrowException(new CannotGetPairsException());
        $this->logger->expects($this->once())->method('error');

        $result = $this->scoreboardService->getFinishedPairs();

        $this->assertEquals(new PairsDTO([]), $result);
    }

    public function upcomingPairsProvider(): Generator
    {
        yield [
            $this->getPairsDTO(MatchStatusEnum::UPCOMING, WinnerEnum::UPCOMING),
            $this->getPairsDTO(MatchStatusEnum::UPCOMING, WinnerEnum::UPCOMING),
        ];
        yield [
            new PairsDTO([]),
            new PairsDTO([]),
        ];
    }

    public function currentPairsProvider(): Generator
    {
        yield [
            $this->getPairsDTO(MatchStatusEnum::IN_PROGRESS, WinnerEnum::HOME),
            $this->getPairsDTO(MatchStatusEnum::IN_PROGRESS, WinnerEnum::HOME),
        ];
        yield [
            new PairsDTO([]),
            new PairsDTO([]),
        ];
    }

    public function finishedPairsProvider(): Generator
    {
        yield [
            $this->getPairsDTO(MatchStatusEnum::FINISHED, WinnerEnum::AWAY),
            $this->getPairsDTO(MatchStatusEnum::FINISHED, WinnerEnum::AWAY),
        ];
        yield [
            new PairsDTO([]),
            new PairsDTO([]),
        ];
    }

    private function getPairsDTO(MatchStatusEnum $matchStatusEnum, WinnerEnum $winnerEnum): PairsDTO
    {
        return new PairsDTO([
            (new PairDTO(
                id: 1,
                homeTeam: CountryTeamEnum::ENGLAND,
                awayTeam: CountryTeamEnum::CROATIA,
                status: $matchStatusEnum,
                result: new ResultDTO(0, 0, $winnerEnum),
            )),
        ]);
    }
}
