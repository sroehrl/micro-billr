<?php

namespace App\Profile;

use App\Auth\Auth;
use App\Auth\BehindLogin;
use Neoan\Routing\Attributes\Web;
use Neoan\Routing\Interfaces\Routable;

#[Web('/profile', 'Profile/views/profile.html', BehindLogin::class)]
class ProfileShow implements Routable
{
    public function __invoke(Auth $auth): array
    {
        return ['user' => $auth->user];
    }
}