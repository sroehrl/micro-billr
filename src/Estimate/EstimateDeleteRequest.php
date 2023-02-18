<?php

namespace App\Estimate;

use Neoan\Request\RequestGuard;

class EstimateDeleteRequest extends RequestGuard
{
    public int $id;
}