<?php

declare(strict_types=1);

namespace App\Domain\Campaign;

use Generator;

interface CampaignDataSourceInterface
{
    /**
     * @return Generator<Campaign>
     */
    public function getCampaigns(): Generator;
}
