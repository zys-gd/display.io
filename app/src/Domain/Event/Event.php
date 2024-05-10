<?php

declare(strict_types=1);

namespace App\Domain\Event;

readonly class Event
{
    public function __construct(
        private EventTypeEnum $type,
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

    public function type(): EventTypeEnum
    {
        return $this->type;
    }
}
