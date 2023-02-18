<?php

namespace App\Project;

use App\Auth\BehindLogin;
use App\Auth\Permission\ProjectPermission;
use App\Bill\BillModel;
use App\Estimate\EstimateItemModel;
use App\Estimate\EstimateModel;
use App\Timeline\TimelineModel;
use Neoan\Errors\NotFound;
use Neoan\Request\Request;
use Neoan\Routing\Attributes\Web;
use Neoan\Routing\Interfaces\Routable;

#[Web('/customer-project/:slug', 'Project/views/public.html', BehindLogin::class)]
class ProjectPublic implements Routable
{
    public function __invoke(): array
    {
        $project = ProjectModel::retrieveOne(['slug' => Request::getParameter('slug')]);
        if(!$project) {
            new NotFound(Request::getRequestUri());
        }
        $estimates = EstimateModel::retrieve(['^deletedAt', 'projectId' => $project->id, 'lockedInAt' => '!'])
            ->each(function (EstimateModel $estimate){
           $estimate->items->each(fn(EstimateItemModel $item) => $item->withProduct()->withMilestone());
        });
        return [
            'documents' => [
                'estimates' => $estimates,
                'bills' => BillModel::retrieve(['^deletedAt','projectId' => $project->id]),
            ],
            'project' => $project->toArray(),
            'timelineItems' => TimelineModel::retrieve(['projectId' => $project->id],['orderBy'=>['createdAt', 'DESC']])->toArray()
        ];
    }
}