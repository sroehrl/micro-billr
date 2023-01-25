<?php

namespace App\Project;

use App\Auth\BehindLogin;
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
    public function __invoke(): array
    {
        $feedback = '';
        if(Request::getRequestMethod() === RequestMethod::POST){
            $newProject = new ProjectModel(Request::getInputs());
            try{
                $newProject->store();
                Response::redirect('/project/'. $newProject->id);
            } catch (\Exception $e){
                $feedback = 'Error saving project';
            }
        }
        return ['feedback' => $feedback];
    }
}