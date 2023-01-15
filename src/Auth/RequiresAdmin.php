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
            Session::restrict([Privilege::ADMIN]);
        } catch (\Exception $e) {
            Response::redirect('/login');
        }
        return $this;
    }
}