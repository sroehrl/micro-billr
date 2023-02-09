<?php

namespace App\Auth;

use Neoan\Errors\Unauthorized;
use Neoan\Routing\Attributes\Get;
use Neoan\Routing\Interfaces\Routable;
use Neoan3\Apps\Session;

#[Get('api/auth/me')]
class Me implements Routable
{
    public function __invoke(): \App\User\UserModel
    {
        if(Session::isLoggedIn()){
            return (new Auth())()->user;
        }
        new Unauthorized();
    }
}