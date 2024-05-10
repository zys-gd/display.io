<?php

use App\Infrastructure\Campaign\CampaignDataSource;
use App\Infrastructure\Event\EventsDataSource;

test('example', function () {
    $job = new \App\Application\Console\OptimizationJob(
        new CampaignDataSource(),
        new EventsDataSource()
    );
    $before = microtime(true);
    $job->run();
    $after = microtime(true);
    expect($after-$before)->toBeLessThan(0.36);
});
