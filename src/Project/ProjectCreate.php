<?php

namespace App\Project;

use App\Auth\Auth;
use App\Auth\BehindLogin;
use App\Timeline\TimelineActivity;
use App\Timeline\TimelineModel;
use Config\FormPost;
use Neoan\Enums\RequestMethod;
use Neoan\Request\Request;
use Neoan\Response\Response;
use Neoan\Routing\Attributes\Web;
use Neoan\Routing\Interfaces\Routable;

#[
    Web('/project/new', 'Project/views/create.html', BehindLogin::class),
    FormPost('/project/new', 'Project/views/create.html', BehindLogin::class)
]
class ProjectCreate implements Routable
{
    public function __invoke(TimelineModel $timeline, Auth $auth): array
    {
        $feedback = '';
        if(Request::getRequestMethod() === RequestMethod::POST){
            $newProject = new ProjectModel(Request::getInputs());
            try{
                $newProject->companyId = $auth->user->companyId;
                $newProject->store();
                $timeline->projectId = $newProject->id;
                $timeline->activity = TimelineActivity::PROJECT_CREATED;
                $timeline->store();
                Response::redirect('/project/'. $newProject->id);
            } catch (\Exception $e){
                $feedback = 'Error saving project';
            }
        }
        return ['feedback' => $feedback];
    }
}