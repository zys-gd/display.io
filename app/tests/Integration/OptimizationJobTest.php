<?php

use App\Infrastructure\Campaign\CampaignDataSource;
use App\Infrastructure\Event\EventsDataSource;

test('performance', function () {
    $job = new \App\Application\Console\OptimizationJob(
        new CampaignDataSource(),
        new EventsDataSource()
    );
    expect($job->run())->toBeLessThan(0.04);
});
