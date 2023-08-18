<?php

namespace tests\Service;

use App\DTO\PairDTO;
use App\DTO\PairsDTO;
use App\DTO\ResultDTO;
use App\Enum\CountryTeamEnum;
use App\Enum\MatchStatusEnum;
use App\Enum\WinnerEnum;
use App\Exception\CannotGetPairsException;
use App\Service\Pairs\PairsProviderFromCache;
use App\Service\Pairs\PairsStorage;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Cache\InvalidArgumentException;
use Symfony\Contracts\Cache\CacheInterface;

class PairsProviderFromCacheTest extends TestCase
{
    private PairsProviderFromCache $pairsProviderFromCache;
    private MockObject|CacheInterface $cache;

    protected function setUp(): void
    {
        $this->cache = $this->createMock(CacheInterface::class);
        $pairsStorage = $this->createMock(PairsStorage::class);

        $this->pairsProviderFromCache = new PairsProviderFromCache(
            $this->cache,
            $pairsStorage,
        );
    }

    public function testGetUpcomingPairs(): void
    {
        $this->cache->method('get')->willReturn($this->getPairsDTO());

        $result = $this->pairsProviderFromCache->getUpcomingPairs();

        $this->assertEquals(new PairsDTO($this->getUpcomingPairs()), $result);
    }

    public function testGetCurrentPairs(): void
    {
        $this->cache->method('get')->willReturn($this->getPairsDTO());

        $result = $this->pairsProviderFromCache->getCurrentPairs();

        $this->assertEquals(new PairsDTO($this->getCurrentPairs()), $result);
    }

    public function testGetFinishedPairs(): void
    {
        $this->cache->method('get')->willReturn($this->getPairsDTO());

        $result = $this->pairsProviderFromCache->getFinishedPairs();

        $this->assertEquals(new PairsDTO($this->getFinishedPairs()), $result);
    }

    public function testGetUpcomingPairsThrowsException(): void
    {
        $this->gettingPairsThrowsException();
        $this->pairsProviderFromCache->getUpcomingPairs();
    }

    public function testGetCurrentPairsThrowsException(): void
    {
        $this->gettingPairsThrowsException();
        $this->pairsProviderFromCache->getCurrentPairs();
    }

    public function testGetFinishedPairsThrowsException(): void
    {
        $this->gettingPairsThrowsException();
        $this->pairsProviderFromCache->getFinishedPairs();
    }


    private function getPairsDTO(): PairsDTO
    {
        return new PairsDTO(
            array_merge(
                $this->getUpcomingPairs(),
                $this->getCurrentPairs(),
                $this->getFinishedPairs(),
            )
        );
    }

    /**
     * @return PairDTO[]
     */
    private function getUpcomingPairs(): array
    {
        return [
            new PairDTO(
                homeTeam: CountryTeamEnum::ENGLAND,
                awayTeam: CountryTeamEnum::CROATIA,
                status: MatchStatusEnum::UPCOMING,
                result: new ResultDTO(0, 0, WinnerEnum::UPCOMING),
            ),
            new PairDTO(
                homeTeam: CountryTeamEnum::BRAZIL,
                awayTeam: CountryTeamEnum::ARGENTINA,
                status: MatchStatusEnum::UPCOMING,
                result: new ResultDTO(0, 0, WinnerEnum::UPCOMING),
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
                homeTeam: CountryTeamEnum::AUSTRIA,
                awayTeam: CountryTeamEnum::HUNGARY,
                status: MatchStatusEnum::IN_PROGRESS,
                result: new ResultDTO(0, 0, WinnerEnum::DRAW),
            ),
            new PairDTO(
                homeTeam: CountryTeamEnum::DENMARK,
                awayTeam: CountryTeamEnum::CZECH_REPUBLIC,
                status: MatchStatusEnum::IN_PROGRESS,
                result: new ResultDTO(1, 0, WinnerEnum::HOME),
            ),
        ];
    }

    /**
     * @return PairDTO[]
     */
    private function getFinishedPairs(): array
    {
        return [
            new PairDTO(
                homeTeam: CountryTeamEnum::POLAND,
                awayTeam: CountryTeamEnum::CANADA,
                status: MatchStatusEnum::FINISHED,
                result: new ResultDTO(0, 0, WinnerEnum::DRAW),
            ),
            new PairDTO(
                homeTeam: CountryTeamEnum::PORTUGAL,
                awayTeam: CountryTeamEnum::NORWAY,
                status: MatchStatusEnum::FINISHED,
                result: new ResultDTO(0, 4, WinnerEnum::AWAY),
            ),
        ];
    }

    public function gettingPairsThrowsException(): void
    {
        $this->cache->method('get')->willThrowException(
            $this->createMock(InvalidArgumentException::class)
        );

        $this->expectException(CannotGetPairsException::class);
    }
}
