<?php

namespace App\Project;

use App\Auth\BehindLogin;
use App\Bill\BillModel;
use App\Bill\CostCalculator;
use App\Milestone\MilestoneModel;
use App\Product\ProductModel;
use App\Timesheet\TimesheetModel;
use Neoan\Database\Database;
use Neoan\Request\Request;
use Neoan\Routing\Attributes\Web;
use Neoan\Routing\Interfaces\Routable;

#[Web('/project/:id/:tab*', 'Project/views/show.html', BehindLogin::class)]
class ProjectShow implements Routable
{
    public function __invoke(): array
    {
        $project = ProjectModel::get(Request::getParameter('id'))->withCustomer()->withPerson();

        return [
            'project' => $project->toArray(),
            'stati' => ProjectStatus::cases(),
            'tab' => Request::getParameter('tab') ?? 'overview',
            'details' => $this->getDetails()
        ];
    }

    private function getDetails(): array
    {
        $projectId = Request::getParameter('id');
        return match (Request::getParameter('tab')){
            'milestones' => MilestoneModel::retrieve(['^deletedAt', 'projectId' => $projectId])->toArray(),
            'overview' => [],
            'tracking' => [
                'timesheets' =>TimesheetModel::retrieve(['^deletedAt', 'projectId' => $projectId],['orderBy' => ['workedAt', 'DESC']])
                    ->each(fn(TimesheetModel $timesheet) => $timesheet->withMilestone())
                    ->each(fn(TimesheetModel $timesheet) => $timesheet->withProduct())->toArray(),
                'products' => ProductModel::retrieve(['^deletedAt'])->toArray(),
                'milestones' => MilestoneModel::retrieve(['^deletedAt', 'projectId' => $projectId])->toArray()
            ],
            'bill' => [
                'existing' => BillModel::retrieve(['projectId' => $projectId, '^deletedAt'],['orderBy' => ['paidAt', 'DESC']])->toArray(),
                'outstandingTotalHours' => CostCalculator::unbilledHoursOnProject($projectId),
                'outstandingTotalNet' => CostCalculator::unbilledNetOnProject($projectId)
            ],
            default => []
        };
    }
}