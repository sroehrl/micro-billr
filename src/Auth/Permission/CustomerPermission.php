<?php

namespace App\Auth\Permission;

use App\Auth\Auth;
use App\Customer\CustomerModel;
use App\Project\ProjectModel;
use Neoan\Errors\Unauthorized;
use Neoan\Request\Request;
use Neoan\Routing\Interfaces\Routable;

class CustomerPermission implements Routable
{
    public function __invoke(Auth $auth): static
    {
        $customerId = Request::getParameter('customerId');
        $authorized = false;
        try{
            $authorized = CustomerModel::get($customerId)->companyId === $auth->user->companyId;

        } catch (\Exception $e) {
            new Unauthorized();
        }
        if(!$authorized) {
            new Unauthorized();
        }
        return $this;
    }
}