<?php

namespace App\Timeline;

use Neoan\Routing\Interfaces\Routable;

class TimelineShow implements Routable
{
    public function __invoke(): array
    {
        return ['name' => 'TimelineShow'];
    }
}