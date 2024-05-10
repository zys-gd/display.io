<?php

declare(strict_types=1);

namespace App\Domain\OptimizationProps;

class OptimizationProps
{
    public function __construct(
        private int $threshold,
        private float $ratioThreshold,
        private string $sourceEvent,
        private string $measuredEvent,
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

    public function sourceEvent(): string
    {
        return $this->sourceEvent;
    }

    public function measuredEvent(): string
    {
        return $this->measuredEvent;
    }
}
