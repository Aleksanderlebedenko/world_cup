<?php

namespace App\Service\Event;

use App\DTO\Event\EventDTO;
use App\DTO\Event\EventsDTO;
use App\DTO\Pair\PairDTO;
use App\DTO\ResultDTO;
use App\Enum\EventTypeEnum;
use App\Enum\MatchStatusEnum;
use App\Enum\WinnerEnum;
use App\Service\GetRandomNumberService;
use App\Service\Pair\PairsProvider;
use App\Service\TimeProvider;

readonly class RandomEventsProvider implements EventsProvider
{
    public function __construct(
        private PairsProvider $pairsProviderFromCache,
        private GetRandomNumberService $getRandomNumberService,
        private TimeProvider $timeProvider,
    ) {
    }

    public function getEvents(): EventsDTO
    {
        $startMatchEvents = $this->generateStartEvents();
        $goalEvents = $this->generateGoalEvents();
        $endMatchEvents = $this->generateFinishEvents();

        return new EventsDTO(
            array_merge(
                $startMatchEvents,
                $goalEvents,
                $endMatchEvents,
            )
        );
    }

    /**
     * @return EventDTO[]|array
     */
    private function generateStartEvents(): array
    {
        $startEvents = [];

        $contOfEvents = $this->getRandomNumberService->getRandomNumber(0, 1);
        if (0 === $contOfEvents) {
            return $startEvents;
        }

        $upcomingPairs = $this->pairsProviderFromCache->getUpcomingPairs();

        $count = 1;
        foreach ($upcomingPairs->pairs as $pair) {
            if ($count > $contOfEvents) {
                break;
            }
            if ($this->isTeamAlreadyPlaying($pair)) {
                continue;
            }

            $pair->result = new ResultDTO(0, 0, WinnerEnum::DRAW);
            $pair->status = MatchStatusEnum::IN_PROGRESS;
            $pair->startDate = $this->timeProvider->getCurrentTime();

            $startEvents[] = new EventDTO(
                EventTypeEnum::START,
                0,
                $pair,
            );

            $count++;
        }

        return $startEvents;
    }

    /**
     * @return EventDTO[]|array
     */
    private function generateGoalEvents(): array
    {
        $goalEvents = [];

        $contOfEvents = $this->getRandomNumberService->getRandomNumber(0, 2);
        if (0 === $contOfEvents) {
            return $goalEvents;
        }

        $upcomingPairs = $this->pairsProviderFromCache->getCurrentPairs();

        $count = 1;
        foreach ($upcomingPairs->pairs as $pair) {
            if ($count > $contOfEvents) {
                break;
            }

            $pair = $this->generateGoal($pair);
            $goalEvents[] = new EventDTO(
                EventTypeEnum::GOAL,
                $this->getRandomNumberService->getRandomNumber(1, 90),
                $pair,
            );

            $count++;
        }

        return $goalEvents;
    }

    /**
     * @return EventDTO[]|array
     */
    private function generateFinishEvents(): array
    {
        $finishEvents = [];

        $currentPairs = $this->pairsProviderFromCache->getCurrentPairs();

        foreach ($currentPairs->pairs as $pair) {
            if ($pair->startDate->getTimestamp() + 10 > $this->timeProvider->getCurrentTime()->getTimestamp()) {
                continue;
            }
            $pair->status = MatchStatusEnum::FINISHED;
            $pair->endDate = $this->timeProvider->getCurrentTime();

            $finishEvents[] = new EventDTO(
                EventTypeEnum::END,
                90,
                $pair,
            );
        }

        return $finishEvents;
    }

    private function generateGoal(PairDTO $pair): PairDTO
    {
        $previousResult = $pair->result;

        $randomNumber = $this->getRandomNumberService->getRandomNumber(0, 1);
        if ($randomNumber) {
            $previousResult->home++;
        } else {
            $previousResult->away++;
        }

        if ($previousResult->home === $previousResult->away) {
            $previousResult->winner = WinnerEnum::DRAW;
        } elseif ($previousResult->home > $previousResult->away) {
            $previousResult->winner = WinnerEnum::HOME;
        } else {
            $previousResult->winner = WinnerEnum::AWAY;
        }

        return $pair;
    }

    private function isTeamAlreadyPlaying(PairDTO $pair): bool
    {
        foreach ($this->pairsProviderFromCache->getCurrentPairs()->pairs as $currentPair) {
            if (
                in_array($currentPair->homeTeam, [$pair->homeTeam, $pair->awayTeam])
                || in_array($currentPair->awayTeam, [$pair->homeTeam, $pair->awayTeam])
            ) {
                return true;
            }
        }

        return false;
    }
}