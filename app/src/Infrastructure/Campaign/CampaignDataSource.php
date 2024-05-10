<?php

declare(strict_types=1);

namespace App\Infrastructure\Campaign;

use App\Domain\Campaign\Campaign;
use App\Domain\Campaign\CampaignDataSourceInterface;
use App\Domain\Event\EventTypeEnum;
use App\Domain\OptimizationProps\OptimizationProps;
use Generator;

use function Pest\Faker\fake;

class CampaignDataSource implements CampaignDataSourceInterface
{
    public function &getCampaigns(): Generator
    {
        // this realisation only for test purpose!
        $publisherIds = [];
        for ($i = 0; $i < 100; $i++) {
            $publisherIds[] = $i;
            yield new Campaign(
                $i,
                new OptimizationProps(
                    fake()->numberBetween(1, 90),
                    fake()->numberBetween(1, 20),
                    fake()->randomElement(
                        array_column(EventTypeEnum::cases(), 'value')
                    ),
                    EventTypeEnum::Purchase->value,
                ),
                $publisherIds
            );
        }
    }
}
