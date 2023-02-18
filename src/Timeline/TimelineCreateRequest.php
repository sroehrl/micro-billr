<?php

namespace App\Timeline;

use Neoan\Request\RequestGuard;

class TimelineCreateRequest extends RequestGuard
{
    public int $projectId;

    public string $content;

    public ?TimelineActivity $activity = TimelineActivity::CUSTOM;
}