<?php

namespace App\Auth;

use App\Helper\FeedbackWrapper;
use Neoan\Routing\Attributes\Get;
use Neoan\Routing\Attributes\Web;
use Neoan\Routing\Interfaces\Routable;
use Neoan3\Apps\Session;

#[Get('/logout', BehindLogin::class)]
class Logout implements Routable
{
    public function __invoke(): array
    {
        Session::logout();
        FeedbackWrapper::redirectBack('logged out');
    }
}