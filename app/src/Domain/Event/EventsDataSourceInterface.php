<?php

declare(strict_types=1);

namespace App\Domain\Event;

use Generator;

interface EventsDataSourceInterface
{
    /**
     * @return Generator<Event>
     */
    public function getEventsSince(string $filter): Generator;
}
