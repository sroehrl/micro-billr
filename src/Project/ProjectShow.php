<?php

namespace App\Project;

use App\Auth\BehindLogin;
use App\Auth\Permission\ProjectPermission;
use App\Bill\BillModel;
use App\Bill\CostCalculator;
use App\Calendar\CalendarEvents;
use App\Estimate\EstimateModel;
use App\Milestone\MilestoneModel;
use App\Note\NoteModel;
use App\Product\ProductModel;
use App\Timeline\TimelineModel;
use App\Timesheet\TimesheetModel;
use Neoan\Database\Database;
use Neoan\Request\Request;
use Neoan\Routing\Attributes\Web;
use Neoan\Routing\Interfaces\Routable;

#[Web('/project/:projectId/:tab*', 'Project/views/show.html', BehindLogin::class, ProjectPermission::class)]
class ProjectShow implements Routable
{
    public function __invoke(): array
    {
        $project = ProjectModel::get(Request::getParameter('projectId'))->withCustomer()->withPerson();

        return [
            'project' => $project->toArray(),
            'stati' => ProjectStatus::cases(),
            'tab' => Request::getParameter('tab') ?? 'overview',
            'details' => $this->getDetails(),
            'events' => json_encode(CalendarEvents::forProject($project->id)),
            'noteType' => 'project',
            'relationId' => $project->id
        ];
    }

    private function getDetails(): array
    {
        $projectId = Request::getParameter('projectId');
        return match (Request::getParameter('tab')){
            'milestones' => MilestoneModel::retrieve(['^deletedAt', 'projectId' => $projectId])->toArray(),
            'estimate' => [
                'estimate' => $this->estimateDetails($projectId),
                'milestones' => MilestoneModel::retrieve(['^deletedAt', 'projectId' => $projectId])->toArray(),
                'products' => ProductModel::retrieve(['^deletedAt'])->toArray(),
            ],
            'tracking' => [
                'timesheets' =>TimesheetModel::retrieve(['^deletedAt', 'projectId' => $projectId],['orderBy' => ['workedAt', 'DESC']])
                    ->each(fn(TimesheetModel $timesheet) => $timesheet->withMilestone())
                    ->each(fn(TimesheetModel $timesheet) => $timesheet->withProduct())->toArray(),
                'products' => ProductModel::retrieve(['^deletedAt'])->toArray(),
                'milestones' => MilestoneModel::retrieve(['^deletedAt', 'projectId' => $projectId])->toArray()
            ],
            'bill' => [
                'existing' => BillModel::retrieve(['projectId' => $projectId],['orderBy' => ['paidAt', 'DESC']])->toArray(),
                'outstandingTotalHours' => CostCalculator::unbilledHoursOnProject($projectId),
                'outstandingTotalNet' => CostCalculator::unbilledNetOnProject($projectId)
            ],
            'timeline' => TimelineModel::retrieve(['projectId' => $projectId],['orderBy' => ['createdAt', 'DESC']])->toArray(),
            default => [
                'notes' => NoteModel::retrieve(['^deletedAt', 'relationId' => $projectId, 'noteType' => 'project'])->toArray(),
            ]
        };
    }

    private function estimateDetails(int $projectId): array
    {
        $estimate = EstimateModel::retrieveOneOrCreate(['projectId' => $projectId, '^deletedAt']);
        $combined = [
            'id' => '?',
            ... $estimate->toArray(),
            'createdStamp' => $estimate->createdAt->stamp,
            'byMilestone' => $estimate->itemsByMilestones()
        ];
        return $combined;
    }

}