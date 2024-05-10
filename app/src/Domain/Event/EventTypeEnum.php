<?php

declare(strict_types=1);

namespace App\Domain\Event;

enum EventTypeEnum: string
{
    case Install = 'install';
    case Purchase = 'purchase';
    case AppOpen = 'app_open';
    case Registration = 'registration';
}
