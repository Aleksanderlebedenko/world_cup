<?php

namespace App\Service\Provider;

use App\DTO\Event\EventDTO;
use App\DTO\Event\EventsDTO;
use App\DTO\Pair\PairDTO;
use App\DTO\ResultDTO;
use App\Enum\EventTypeEnum;
use App\Enum\MatchStatusEnum;
use App\Enum\WinnerEnum;
use App\Service\Provider\EventPairsSynchronizer;
use App\Service\Event\EventsProvider;
use App\Service\Pair\PairsProvider;
use DateTimeImmutable;

/**
 * This class is responsible for generating random events.
 * Doesn't need to be tested because it doesn't make much sense, as it exists only for expose the result.
 * Also, it violates SRP and CQS principles deliberately, because in real life such kind of classes wouldn't exist.
 * Let's consider this class as a faker of data. Which do some aside work for syncing our data storage.
 */
readonly class EventsProviderGenerator implements EventsProvider
{
    public function __construct(
        private EventPairsSynchronizer $eventPairsSynchronizer,
        private PairsProvider $pairsProviderFromCache,
    ) {
    }

    public function getEvents(): EventsDTO
    {
        $events = $this->generateEvents();

        $this->syncEventsWithPairs($events); // Triggering sync should be in the another place

        return $events;
    }

    private function generateEvents(): EventsDTO
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

        $contOfEvents = rand(0, 1);
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
            $pair->startDate = new DateTimeImmutable('now');

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

        $contOfEvents = rand(0, 2);
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
                rand(1, 90), //need more calculation, but from test case perspective I decided to not do it yet
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
            if ($pair->startDate->getTimestamp() + 10 > (new DateTimeImmutable('now'))->getTimestamp()) {
                continue;
            }
            $pair->status = MatchStatusEnum::FINISHED;
            $pair->endDate = new DateTimeImmutable('now');

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

        $randomNumber = rand(0, 1);
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

    /**
     * In the real life this is not needed, because synchronization of pairs state and upcoming events
     * should be triggered not from this place, but from data consistency perspective, I've put it here,
     * just for making it work.
     * But I understand that this violates SRP and CQS principles.
     */
    private function syncEventsWithPairs(
        EventsDTO $eventsDTO
    ): void {
        $this->eventPairsSynchronizer->sync($eventsDTO);
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