<?php

declare(strict_types=1);

namespace App\Application\Console;

use App\Domain\Campaign\Campaign;
use App\Domain\Campaign\CampaignDataSourceInterface;
use App\Domain\Event\EventsDataSourceInterface;
use App\Infrastructure\EventDispatcher\EventDispatcher;
use AppendIterator;

class OptimizationJob
{
    public function __construct(
        private readonly CampaignDataSourceInterface $campaignDataSource,
        private readonly EventsDataSourceInterface $eventsDataSource,
    ) {
    }

    public function run(): void
    {
        $campaigns = $this->campaignDataSource->getCampaigns();

        /** @var array<int, Campaign> $formattedCampaigns */
        $formattedCampaigns = [];
        foreach ($campaigns as &$campaign) {
            $formattedCampaigns[$campaign->id()] = $campaign;
        }

        foreach ($this->eventsDataSource->getEventsSince("2 weeks ago") as &$event) {
            if (isset($formattedCampaigns[$event->campaignId()])) {
                $formattedCampaigns[$event->campaignId()]->processEvent(
                    $event->campaignId(),
                    $event->publisherId(),
                    $event->type()
                );
            }
        }

        $publisherEvents = new AppendIterator();
        foreach ($formattedCampaigns as &$campaign) {
            $campaign->processEvents();
            $campaign->saveBlacklist(); // basically should be out of circle, but it is ActiveRecord, right?
            $publisherEvents->append($campaign->dispatchingEvents());
        }
        // dispatch all system events. In our case- events for email notification.
        // Or we can call MailSender directly for performance.
        // But it increase coupling
        (new EventDispatcher())->dispatch($publisherEvents);
    }
}
