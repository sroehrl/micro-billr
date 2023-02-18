<?php

namespace App\Auth\Permission;

use App\Auth\Auth;
use App\Customer\CustomerModel;
use App\Milestone\MilestoneModel;
use Neoan\Errors\Unauthorized;
use Neoan\Request\Request;
use Neoan\Routing\Interfaces\Routable;

class MilestonePermission implements Routable
{
    public function __invoke(Auth $auth): static
    {
        $authorized = false;
        try{
            $authorized = MilestoneModel::get(Request::getParameter('milestoneId'))->project()->companyId === $auth->user->companyId;

        } catch (\Exception $e) {
            new Unauthorized();
        }
        if(!$authorized) {
            new Unauthorized();
        }
        return $this;
    }
}