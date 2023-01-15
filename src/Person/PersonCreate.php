<?php

namespace App\Person;

use App\Auth\BehindLogin;
use Neoan\Routing\Attributes\Web;
use Neoan\Routing\Interfaces\Routable;

#[Web('/person/new/:customerId*', 'Person/views/create.html', BehindLogin::class)]
class PersonCreate implements Routable
{
    public function __invoke(): array
    {
        return ['name' => 'PersonCreate'];
    }
}