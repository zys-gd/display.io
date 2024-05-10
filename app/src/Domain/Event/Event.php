<?php

declare(strict_types=1);

namespace App\Domain\Event;

readonly class Event
{
    public function __construct(
        private string $type,
        private int $campaignId,
        private int $publisherId,
    ) {
    }

    public function campaignId(): int
    {
        return $this->campaignId;
    }

    public function publisherId(): int
    {
        return $this->publisherId;
    }

    public function type(): string
    {
        return $this->type;
    }
}
