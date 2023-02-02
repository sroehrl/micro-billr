<?php

namespace App\Milestone;

use App\Auth\BehindLogin;
use App\Timesheet\TimesheetModel;
use Neoan\Request\Request;
use Neoan\Routing\Attributes\Web;
use Neoan\Routing\Interfaces\Routable;

#[Web('/milestone/:id', 'Milestone/views/show.html', BehindLogin::class)]
class MilestoneShow implements Routable
{
    public function __invoke(): array
    {
        $milestoneId =  Request::getParameter('id');
        $milestone = MilestoneModel::get($milestoneId)->withProject();

        $timeSheets = TimesheetModel::retrieve(['^deletedAt', 'milestoneId' => $milestoneId],['orderBy'=> ['workedAt', 'DESC']]);



        $chartData = [
            'labels' => array_map(fn($timeSheet) => $timeSheet['productName'], $timeSheets->toArray()),
            'datasets' => [
                ['label' => 'Estimate', 'data' => array_fill(0, $timeSheets->count(), $milestone->estimatedHours), 'type' => 'line'],
                ['label' => 'Product', 'data' => array_map(fn ($timeSheet) => $timeSheet['hours'], $timeSheets->toArray())],
                ['label' => 'Actual', 'data' => array_fill(0, $timeSheets->count(), $milestone->actualHours), $timeSheets->toArray(), 'type' => 'line'],
            ]
        ];

        return [
            ...$milestone->toArray(),
            'noteType' => 'milestone',
            'relationId' => $milestoneId,
            'chartData' => json_encode($chartData),
            'timesheets' => $timeSheets->toArray()
        ];
    }
}