<?php

namespace App\Milestone\Request;

use Neoan\Model\Helper\DateTimeProperty;
use Neoan\Request\RequestGuard;

class MilestoneRequest extends RequestGuard
{
    public int $projectId;

    public string $title;

    public ?DateTimeProperty $targetedAt;


}