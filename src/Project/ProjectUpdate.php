<?php

namespace App\Project;

use App\Auth\BehindLogin;
use App\Helper\FeedbackWrapper;
use Neoan\Request\Request;
use Neoan\Response\Response;
use Neoan\Routing\Attributes\Post;
use Neoan\Routing\Interfaces\Routable;

#[Post('/project/:id', BehindLogin::class)]
class ProjectUpdate implements Routable
{
    public function __invoke(): array
    {
        $project = ProjectModel::get(Request::getParameter('id'));
        $project->status = ProjectStatus::from(Request::getInput('status'));
        $project->targetedAt->set(Request::getInput('targetedAt'));
        $project->startedAt->set(Request::getInput('startedAt'));
        $project->store();
        FeedbackWrapper::redirectBack('Project updated');
    }
}