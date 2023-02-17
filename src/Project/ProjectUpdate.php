<?php

namespace App\Project;

use App\Auth\BehindLogin;
use App\Helper\FeedbackWrapper;
use App\Timeline\TimelineActivity;
use App\Timeline\TimelineModel;
use Neoan\Request\Request;
use Neoan\Response\Response;
use Neoan\Routing\Attributes\Post;
use Neoan\Routing\Interfaces\Routable;

#[Post('/project/:id', BehindLogin::class)]
class ProjectUpdate implements Routable
{
    public function __invoke(TimelineModel $timeline): array
    {
        $project = ProjectModel::get(Request::getParameter('id'));
        $status= ProjectStatus::from(Request::getInput('status'));
        if($status !== $project->status){
            $timeline->projectId = $project->id;
            $timeline->activity = TimelineActivity::PROJECT_STATUS_CHANGED;
            $timeline->content = $project->status->name . ' -> ' . $status->name;
            $timeline->store();
        }
        $project->status = $status;
        $project->targetedAt->set(Request::getInput('targetedAt'));
        $project->startedAt->set(Request::getInput('startedAt'));
        $project->store();
        FeedbackWrapper::redirectBack('Project updated');
    }
}