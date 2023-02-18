<?php

namespace App\Auth\Permission;

use App\Auth\Auth;
use App\Project\ProjectModel;
use Neoan\Errors\Unauthorized;
use Neoan\Request\Request;
use Neoan\Routing\Interfaces\Routable;

class ProjectPermission implements Routable
{
    public function __invoke(Auth $auth): static
    {
        $projectId = Request::getParameter('projectId');
        $authorized = false;
        try{
            $authorized = ProjectModel::get($projectId)->companyId === $auth->user->companyId;

        } catch (\Exception $e) {
            new Unauthorized();
        }
        if(!$authorized) {
            new Unauthorized();
        }
        return $this;
    }
}