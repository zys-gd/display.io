<?php

declare(strict_types=1);

use App\Domain\Campaign\Campaign;
use App\Domain\Event\EventTypeEnum;
use App\Domain\OptimizationProps\OptimizationProps;

test('campaign behaviour', function () {
    $bannedPublisherId = 1;
    $publisherIdForBan = 2;
    $publisherIdSkip = 3;

    $campaign = new Campaign(
        1,
        new OptimizationProps(
            4,
            0.2,
            EventTypeEnum::Install,
            EventTypeEnum::Purchase,
        ),
        [$bannedPublisherId, $publisherIdSkip]
    );


    for ($i = 0; $i < 10; $i++) {
        $campaign->collectEvent(
            1,
            $bannedPublisherId,
            EventTypeEnum::Install,
        );
    }
    for ($i = 0; $i < 5; $i++) {
        $campaign->collectEvent(
            1,
            $bannedPublisherId,
            EventTypeEnum::Purchase,
        );
    }

    for ($i = 0; $i < 15; $i++) {
        $campaign->collectEvent(
            1,
            $publisherIdForBan,
            EventTypeEnum::Install,
        );
    }
    for ($i = 0; $i < 2; $i++) {
        $campaign->collectEvent(
            1,
            $publisherIdForBan,
            EventTypeEnum::Purchase,
        );
    }

    for ($i = 0; $i < 1; $i++) {
        $campaign->collectEvent(
            1,
            $publisherIdSkip,
            EventTypeEnum::Purchase,
        );
    }
    for ($i = 0; $i < 2; $i++) {
        $campaign->collectEvent(
            1,
            $publisherIdSkip,
            EventTypeEnum::Install,
        );
    }

    $campaign->processCollectedEvents();
    $result = $campaign->dispatchingEvents();
    expect($result->getArrayCopy())->toBe([
        [
            'payload' => '{"publisher_id"=>"1","campaign_id"=>"1"}',
            'event_name' => 'publisher_unbanned',
        ],
        [
            'payload' => '{"publisher_id"=>"2","campaign_id"=>"1"}',
            'event_name' => 'publisher_banned',
        ],
    ]);
});
