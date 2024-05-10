<?php

declare(strict_types=1);

namespace App\Infrastructure\Event;

use App\Domain\Event\Event;
use App\Domain\Event\EventsDataSourceInterface;
use App\Domain\Event\EventTypeEnum;
use Generator;

use function Pest\Faker\fake;

class EventsDataSource implements EventsDataSourceInterface
{
    // this realisation only for test purpose!
    public function &getEventsSince(string $filter): Generator
    {
        for ($i = 0; $i < 2000; $i++) {
            yield new Event(
                fake()->randomElement(
                    array_column(EventTypeEnum::cases(), 'value')
                ),
                fake()->numberBetween(1, 100),
                fake()->numberBetween(1, 100)
            );
        }
    }
}
