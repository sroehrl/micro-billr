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
        return [
            ...MilestoneModel::get($milestoneId)->withProject()->toArray(),
            'noteType' => 'milestone',
            'relationId' => $milestoneId,
            'timesheets' => TimesheetModel::retrieve(['^deletedAt', 'milestoneId' => $milestoneId],['orderBy'=> ['workedAt', 'DESC']])->toArray()
        ];
    }
}