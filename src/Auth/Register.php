<?php

namespace App\Auth;

use Neoan\Routing\Attributes\Web;
use Neoan\Routing\Interfaces\Routable;

#[Web('/register', 'Auth/views/register.html')]
class Register implements Routable
{
    public function __invoke(): array
    {
        return ['name' => 'Register'];
    }
}