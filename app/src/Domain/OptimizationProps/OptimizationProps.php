<?php

declare(strict_types=1);

namespace App\Domain\OptimizationProps;

use App\Domain\Event\EventTypeEnum;

class OptimizationProps
{
    public function __construct(
        private int $threshold,
        private float $ratioThreshold,
        private EventTypeEnum $sourceEvent,
        private EventTypeEnum $measuredEvent,
    ) {
    }

    public function threshold(): int
    {
        return $this->threshold;
    }

    public function ratioThreshold(): float
    {
        return $this->ratioThreshold;
    }

    public function sourceEvent(): EventTypeEnum
    {
        return $this->sourceEvent;
    }

    public function measuredEvent(): EventTypeEnum
    {
        return $this->measuredEvent;
    }
}
