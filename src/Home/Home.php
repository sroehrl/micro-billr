<?php

namespace App\Home;

use App\Auth\Auth;
use App\Auth\BehindLogin;
use App\Bill\BillModel;
use App\Calendar\CalendarEvents;
use App\Person\PersonModel;
use App\Project\ProjectModel;
use App\Project\ProjectStatus;
use App\Timesheet\TimesheetModel;
use App\User\Privilege;
use Neoan\Database\Database;
use Neoan\Response\Response;
use Neoan\Routing\Attributes\Web;
use Neoan\Routing\Interfaces\Routable;
use Neoan\Store\Store;

#[Web('/', 'Home/home.html', BehindLogin::class)]
class Home implements Routable
{
    public function __invoke(Auth $auth): array
    {
        Store::write('pageTitle', 'Welcome');

        if($auth->user->privilege === Privilege::CUSTOMER){
            Response::html([
                'person' => PersonModel::get($auth->user->personId),
                'company' => $auth->user->company()
            ], 'Home/customer.html');
        }

        $myProjects = Database::easy('project_model.id', ['^deletedAt', 'companyId' => $auth->user->companyId]);
        $projectIds = array_map(fn($row) => $row['id'], $myProjects);

        $unbilledHours = 0;
        TimesheetModel::retrieve(['^billId','^deletedAt'])->each(function(TimesheetModel $t) use(&$unbilledHours, $projectIds){
            //TODO: make this more performant in the future
            if(in_array($t->projectId, $projectIds)){
                $unbilledHours = $unbilledHours + $t->hours;
            }

        } );
        return [
            'openProjects' => ProjectModel::retrieve([
                '^deletedAt',
                'companyId' => $auth->user->companyId,
                'status' => ProjectStatus::IN_PROGRESS->value
            ])->count(),
            'plannedProjects' => ProjectModel::retrieve([
                '^deletedAt',
                'companyId' => $auth->user->companyId,
                'status' => ProjectStatus::PLANNED->value
            ])->count(),
            'unbilledHours' => $unbilledHours,
            'openBills' => BillModel::retrieve(['^deletedAt', '^paidAt', 'companyId' => $auth->user->companyId])->count(),
            'events' => json_encode(CalendarEvents::allEvents($auth->user->companyId))
        ];
    }
}