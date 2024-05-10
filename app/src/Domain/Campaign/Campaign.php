<?php

declare(strict_types=1);

namespace App\Domain\Campaign;


use App\Domain\Event\EventTypeEnum;
use App\Domain\OptimizationProps\OptimizationProps;
use ArrayIterator;

class Campaign
{
    private int $id;
    private OptimizationProps $optProps;
    private array $publisherBlacklist;

    private array $processedEvents;
    private ArrayIterator $dispatchingEvents;

    // I need it to fake DataSource only
    public function __construct(int $id, OptimizationProps $optProps, array $publisherBlacklist)
    {
        $this->id = $id;
        $this->optProps = $optProps;
        $this->publisherBlacklist = $publisherBlacklist;
        $this->dispatchingEvents = new ArrayIterator();
        $this->processedEvents = [];
    }

    public function saveBlacklist(): void
    {
        // dont implement
    }

    public function id(): int
    {
        return $this->id;
    }

    public function collectEvent(int $campaignId, int $publisherId, EventTypeEnum $type): void
    {
        if ($campaignId === $this->id && $type === $this->optProps->sourceEvent()) {
            $sourceEvent = $this->optProps->sourceEvent()->value;
            isset($this->processedEvents[$publisherId][$sourceEvent])
                ? $this->processedEvents[$publisherId][$sourceEvent]++
                : $this->processedEvents[$publisherId][$sourceEvent] = 1;
        }

        if ($campaignId === $this->id && $type === $this->optProps->measuredEvent()) {
            $measuredEvent = $this->optProps->measuredEvent()->value;
            isset($this->processedEvents[$publisherId][$measuredEvent])
                ? $this->processedEvents[$publisherId][$measuredEvent]++
                : $this->processedEvents[$publisherId][$measuredEvent] = 1;
        }
    }

    public function processCollectedEvents(): void
    {
        foreach ($this->processedEvents as $publisherId => $events) {
            $sourceEvent = $this->optProps->sourceEvent()->value;
            $isCrossedThreshold = isset($events[$sourceEvent]) && $events[$sourceEvent] >= $this->optProps->threshold();
            if (!$isCrossedThreshold) {
                // if a publisher has less sourceEvents that the threshold, then she should not be blacklisted
                continue;
            }
            $measuredEvent = $this->optProps->measuredEvent()->value;
            $ratioThreshold = isset($events[$sourceEvent]) && isset($events[$measuredEvent])
                ? $events[$measuredEvent] / $events[$sourceEvent]
                : null;

            $isPublisherBanned = isset(array_flip($this->publisherBlacklist)[$publisherId]);
            if ($ratioThreshold && !$isPublisherBanned && $ratioThreshold < $this->optProps->ratioThreshold()) {
                $this->publisherBlacklist[] = $publisherId;
                // it must be some object, but for demo I keep array just as example
                $this->dispatchingEvents->append([
                    'payload' => sprintf('{"publisher_id"=>"%s","campaign_id"=>"%s"}', $publisherId, $this->id),
                    'event_name' => 'publisher_banned',
                ]);
            }

            if ($ratioThreshold && $isPublisherBanned && $ratioThreshold >= $this->optProps->ratioThreshold()) {
                unset($this->publisherBlacklist[array_flip($this->publisherBlacklist)[$publisherId]]);
                $this->dispatchingEvents->append([
                    'payload' => sprintf('{"publisher_id"=>"%s","campaign_id"=>"%s"}', $publisherId, $this->id),
                    'event_name' => 'publisher_unbanned',
                ]);
            }
        }
    }

    public function dispatchingEvents(): ArrayIterator
    {
        return $this->dispatchingEvents;
    }
}
