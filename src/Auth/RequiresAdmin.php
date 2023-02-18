<?php

namespace App\Auth;

use App\User\Privilege;
use App\User\UserModel;
use Neoan\Response\Response;
use Neoan\Routing\Interfaces\Routable;
use Neoan3\Apps\Session;

class RequiresAdmin implements Routable
{
    public UserModel $user;
    public function __invoke(array $provided = []): static
    {
        try{
            $session = Session::restrict([Privilege::ADMIN]);
            $this->user = UserModel::get(Session::userId());
        } catch (\Exception $e) {
            Response::redirect('/');
        }
        return $this;
    }
}