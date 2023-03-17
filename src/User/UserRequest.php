<?php

namespace App\User;

use Neoan\Request\RequestGuard;

class UserRequest extends RequestGuard
{
    public string $email;

    public ?string $password = null;
}