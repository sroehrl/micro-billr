<?php

namespace App\Auth;

use Neoan\Response\Response;
use Neoan\Routing\Interfaces\Routable;
use Neoan3\Apps\Session;

class BehindLogin implements Routable
{
    public function __invoke(): array
    {
        try{
            return Session::restrict();
        } catch (\Exception $e) {
            Response::redirect('/login');
        }

    }
}