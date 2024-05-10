<?php

declare(strict_types=1);

namespace App\Infrastructure\Event;

use App\Domain\Event\Event;
use App\Domain\Event\EventsDataSourceInterface;
use App\Domain\Event\EventTypeEnum;

use function Pest\Faker\fake;

class EventsDataSource implements EventsDataSourceInterface
{
    // this realisation only for test purpose!
    public function &getEventsSince(string $filter): array
    {
        $r = [];
        for ($i = 0; $i < 2000; $i++) {
            $r[] = new Event(
                fake()->randomElement(
                    EventTypeEnum::cases()
                ),
                fake()->numberBetween(1, 1000),
                fake()->numberBetween(1, 100)
            );
        }

        return $r;
    }
}
