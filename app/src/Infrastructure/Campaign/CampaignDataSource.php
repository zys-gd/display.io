<?php

declare(strict_types=1);

namespace App\Infrastructure\Campaign;

use App\Domain\Campaign\Campaign;
use App\Domain\Campaign\CampaignDataSourceInterface;
use App\Domain\Event\EventTypeEnum;
use App\Domain\OptimizationProps\OptimizationProps;

use function array_unique;
use function Pest\Faker\fake;

class CampaignDataSource implements CampaignDataSourceInterface
{
    // this realisation only for test purpose!
    public function &getCampaigns(): array
    {
        $r = [];
        $publisherIds = [];
        for ($i = 0; $i < 100; $i++) {
            $publisherIds[] = fake()->numberBetween(1, 100);
        }
        for ($i = 0; $i < 1000; $i++) {
            $r[] = new Campaign(
                $i,
                new OptimizationProps(
                    fake()->numberBetween(1, 90),
                    fake()->numberBetween(1, 20),
                    fake()->randomElement(
                        array_column(EventTypeEnum::cases(), 'value')
                    ),
                    EventTypeEnum::Purchase->value,
                ),
                array_unique($publisherIds)
            );
        }

        return $r;
    }
}
