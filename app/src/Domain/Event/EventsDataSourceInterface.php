<?php

declare(strict_types=1);

namespace App\Domain\Event;

interface EventsDataSourceInterface
{
    /**
     * @return Event[]
     */
    public function getEventsSince(string $filter): array;
}
