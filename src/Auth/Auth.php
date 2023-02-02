<?php

namespace App\Auth;

use App\User\UserModel;
use Neoan\Routing\Interfaces\Routable;
use Neoan3\Apps\Session;

class Auth implements Routable
{
    public UserModel $user;
    public function __invoke(): Auth
    {
        $this->user = UserModel::get(Session::userId());
        return $this;
    }
}