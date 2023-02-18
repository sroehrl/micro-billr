<?php

namespace App\Auth\Permission;

use App\Auth\Auth;
use App\Person\PersonModel;
use Neoan\Errors\Unauthorized;
use Neoan\Request\Request;
use Neoan\Routing\Interfaces\Routable;

class PersonPermission implements Routable
{
    public function __invoke(Auth $auth): static
    {
        $personId = Request::getParameter('personId');
        try{
            if(PersonModel::get($personId)->customer()->companyId !== $auth->user->companyId){
                new Unauthorized();
            }

        } catch (\Exception $e) {
            new Unauthorized();
        }
        return $this;
    }
}