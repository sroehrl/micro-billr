<?php

namespace App\Auth;

use App\User\UserModel;
use Neoan\Request\Request;
use Neoan\Response\Response;
use Neoan\Routing\Interfaces\Routable;
use Neoan3\Apps\Session;

class BehindLogin implements Routable
{
    public UserModel $user;
    public function __invoke(): static
    {

        try{
            $session = Session::restrict();
            $this->user = UserModel::get(Session::userId());
            setcookie(session_name(),session_id(),time()+3600);
            return $this;
        } catch (\Exception $e) {
            setcookie('afterLogin', Request::getRequestUri(), time() + 3600, '/');
            usleep(10);
            Response::redirect('/login');
        }

    }
}