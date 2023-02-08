<?php

namespace App\Home;

use App\Auth\BehindLogin;
use App\Bill\BillModel;
use App\Calendar\CalendarEvents;
use App\Project\ProjectModel;
use App\Project\ProjectStatus;
use App\Timesheet\TimesheetModel;
use Neoan\Routing\Attributes\Web;
use Neoan\Routing\Interfaces\Routable;
use Neoan\Store\Store;

#[Web('/', 'Home/home.html', BehindLogin::class)]
class Home implements Routable
{
    public function __invoke(): array
    {
        Store::write('pageTitle', 'Welcome');
        $unbilledHours = 0;
        TimesheetModel::retrieve(['^billId','^deletedAt'])->each(function(TimesheetModel $t) use(&$unbilledHours){
            $unbilledHours = $unbilledHours + $t->hours;
        } );
        return [
            'openProjects' => ProjectModel::retrieve([
                '^deletedAt',
                'status' => ProjectStatus::IN_PROGRESS->value
            ])->count(),
            'plannedProjects' => ProjectModel::retrieve([
                '^deletedAt',
                'status' => ProjectStatus::PLANNED->value
            ])->count(),
            'unbilledHours' => $unbilledHours,
            'openBills' => BillModel::retrieve(['^deletedAt', '^paidAt'])->count(),
            'events' => json_encode(CalendarEvents::allEvents())
        ];
    }
}