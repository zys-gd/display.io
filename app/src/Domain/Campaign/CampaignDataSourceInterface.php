<?php

declare(strict_types=1);

namespace App\Domain\Campaign;

interface CampaignDataSourceInterface
{
    /**
     * @return Campaign[]
     */
    public function getCampaigns(): array;
}
