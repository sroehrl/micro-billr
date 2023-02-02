<?php

namespace App\TimeSheet;

use App\Auth\BehindLogin;
use Neoan\Model\Collection;
use Neoan\Request\Request;
use Neoan\Routing\Attributes\Get;
use Neoan\Routing\Interfaces\Routable;

#[Get('/api/timesheet', BehindLogin::class)]
class TimesheetGet implements Routable
{
    public function __invoke(): Collection
    {
        return TimesheetModel::retrieve([...Request::getQueries(), '^deletedAt'])->each(function(TimesheetModel $model){
            $model->withProduct()->withMilestone();
        });
    }
}