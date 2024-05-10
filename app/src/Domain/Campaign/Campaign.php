<?php

declare(strict_types=1);

namespace App\Domain\Campaign;


use App\Domain\OptimizationProps\OptimizationProps;
use ArrayIterator;

class Campaign
{
    private int $id;
    private OptimizationProps $optProps;
    private array $publisherBlacklist;

    /** @var array */
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

    public function collectEvent(int $campaignId, int $publisherId, string $type): void
    {
        if ($campaignId === $this->id && $type === $this->optProps->sourceEvent()) {
            isset($this->processedEvents[$publisherId][$this->optProps->sourceEvent()])
                ? $this->processedEvents[$publisherId][$this->optProps->sourceEvent()]++
                : $this->processedEvents[$publisherId][$this->optProps->sourceEvent()] = 1;
        }

        if ($campaignId === $this->id && $type === $this->optProps->measuredEvent()) {
            isset($this->processedEvents[$publisherId][$this->optProps->measuredEvent()])
                ? $this->processedEvents[$publisherId][$this->optProps->measuredEvent()]++
                : $this->processedEvents[$publisherId][$this->optProps->measuredEvent()] = 1;
        }
    }

    public function processCollectedEvents(): void
    {
        foreach ($this->processedEvents as $publisherId => $events) {
            $isCrossedThreshold = isset($events[$this->optProps->sourceEvent()])
                                  && $events[$this->optProps->sourceEvent()] >= $this->optProps->threshold();
            if (!$isCrossedThreshold) {
                // if a publisher has less sourceEvents that the threshold, then she should not be blacklisted
                continue;
            }
            $ratioThreshold = isset($events[$this->optProps->sourceEvent()])
                              && isset($events[$this->optProps->measuredEvent()])
                ? $events[$this->optProps->measuredEvent()] / $events[$this->optProps->sourceEvent()]
                : null;

            $isPublisherBanned = isset(array_flip($this->publisherBlacklist)[$publisherId]);
            if ($ratioThreshold && !$isPublisherBanned && $ratioThreshold < $this->optProps->ratioThreshold()) {
                $this->publisherBlacklist[] = $publisherId;
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
