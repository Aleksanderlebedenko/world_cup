<?php

namespace App\Service\Pair;

use App\DTO\Pair\PairDTO;
use App\DTO\Pair\PairsDTO;
use App\DTO\ResultDTO;
use App\Enum\CountryTeamEnum;
use App\Enum\MatchStatusEnum;
use App\Enum\WinnerEnum;
use DateTimeImmutable;

/**
 * Class implementation is intended to mimic a database or third party API responses for initial load,
 * next data will come from the cache.
 * Don't need to be tested, I use it only for getting data purposes.
 */
class PairsStorage
{
    private const MATCH_DURATION = 20;

    public function getPairs(): PairsDTO
    {
        $pairs = [];
        $id = 1;

        foreach (CountryTeamEnum::cases() as $homeTeam) {
            foreach (CountryTeamEnum::cases() as $awayTeam) {
                if ($homeTeam->name === $awayTeam->name) {
                    continue;
                }

                $matchStatus = MatchStatusEnum::cases()[rand(0, count(MatchStatusEnum::cases()) - 1)];

                $result = $this->createResult($matchStatus);
                $startTime = $this->createStartMatchDate($matchStatus);
                $endTime = $this->createEndMatchDate($matchStatus, $startTime);

                $pair = new PairDTO(
                    $id++,
                    $homeTeam,
                    $awayTeam,
                    $matchStatus,
                    $result,
                    $startTime,
                    $endTime,
                );

                if ($this->isTeamAlreadyPlaying($pairs, $pair)) {
                    continue;
                }

                $pairs[] = $pair;
            }
        }

        return new PairsDTO($pairs);
    }

    private function createResult(MatchStatusEnum $matchStatus): ResultDTO
    {
        if ($matchStatus->name === MatchStatusEnum::UPCOMING->name) {
            return new ResultDTO(0, 0, WinnerEnum::UPCOMING);
        }

        $homeTeamGoals = rand(0, 5);
        $awayTeamGoals = rand(0, 5);

        if ($homeTeamGoals === $awayTeamGoals) {
            return new ResultDTO($homeTeamGoals, $awayTeamGoals, WinnerEnum::DRAW);
        }

        if ($homeTeamGoals > $awayTeamGoals) {
            return new ResultDTO($homeTeamGoals, $awayTeamGoals, WinnerEnum::HOME);
        }

        return new ResultDTO($homeTeamGoals, $awayTeamGoals, WinnerEnum::AWAY);
    }

    private function createStartMatchDate(MatchStatusEnum $matchStatus): ?DateTimeImmutable
    {
        if ($matchStatus->name === MatchStatusEnum::UPCOMING->name) {
            return new DateTimeImmutable(sprintf('now + %d seconds', rand(10, 3600)));
        }

        if ($matchStatus->name === MatchStatusEnum::IN_PROGRESS->name) {
            return new DateTimeImmutable('now ');
        }

        return null;
    }

    private function createEndMatchDate(MatchStatusEnum $matchStatus, ?DateTimeImmutable $startTime): ?DateTimeImmutable
    {
        if ($matchStatus->name === MatchStatusEnum::UPCOMING->name) {
            return null;
        }

        return $startTime?->modify(sprintf('+ %d seconds', self::MATCH_DURATION));
    }

    private function isTeamAlreadyPlaying(array $pairs, PairDTO $pair): bool
    {
        if ($pair->status->name !== MatchStatusEnum::IN_PROGRESS->name) {
            return false;
        }

        foreach ($pairs as $existingPair) {
            if ($existingPair->status->name !== MatchStatusEnum::IN_PROGRESS->name) {
                continue;
            }

            if ($existingPair->homeTeam->name === $pair->homeTeam->name
                || $existingPair->homeTeam->name === $pair->awayTeam->name
                || $existingPair->awayTeam->name === $pair->homeTeam->name
                || $existingPair->awayTeam->name === $pair->awayTeam->name
            ) {
                return true;
            }
        }

        return false;
    }
}