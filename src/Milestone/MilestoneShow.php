<?php

namespace App\Milestone;

use App\Auth\BehindLogin;
use App\Auth\Permission\MilestonePermission;
use App\Product\ProductModel;
use App\Timesheet\TimesheetModel;
use Neoan\Request\Request;
use Neoan\Routing\Attributes\Web;
use Neoan\Routing\Interfaces\Routable;

#[Web('/milestone/:milestoneId', 'Milestone/views/show.html', BehindLogin::class, MilestonePermission::class)]
class MilestoneShow implements Routable
{
    public function __invoke(): array
    {
        $milestoneId =  Request::getParameter('milestoneId');
        $milestone = MilestoneModel::get($milestoneId)->withProject();

        $timeSheets = TimesheetModel::retrieve(['^deletedAt', 'milestoneId' => $milestoneId],['orderBy'=> ['workedAt', 'DESC']]);





        return [
            ...$milestone->toArray(),
            'noteType' => 'milestone',
            'details' => [
                'milestones' => [$milestone->toArray()],
                'products' => ProductModel::retrieve(['^deletedAt'])->toArray()
            ],
            'project' => $milestone->project()->toArray(),
            'relationId' => $milestoneId,
            'chartData' => '/milestone/' . $milestoneId,
            'timesheets' => $timeSheets->toArray()
        ];
    }
}