<?php

namespace App\Profile;

use App\Auth\BehindLogin;
use App\User\UserRequest;
use Neoan\Routing\Attributes\Post;
use Neoan\Routing\Interfaces\Routable;

#[Post('/profile', BehindLogin::class)]
class ProfileUpdate implements Routable
{
    public function __invoke(UserRequest $request): array
    {
        return (array) $request;
    }
}