<?php

namespace App\Home;

use App\Auth\BehindLogin;
use Neoan\Routing\Attributes\Web;
use Neoan\Routing\Interfaces\Routable;
use Neoan\Store\Store;

#[Web('/', 'Home/home.html', BehindLogin::class)]
class Home implements Routable
{
    public function __invoke(): array
    {
        Store::write('pageTitle', 'Welcome');
        return ['name' => 'Home'];
    }
}