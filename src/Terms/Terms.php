<?php

namespace App\Terms;

use Neoan\Routing\Attributes\Web;
use Neoan\Routing\Interfaces\Routable;
use Neoan\Store\Store;

#[Web('/terms', 'Terms/terms.html')]
class Terms implements Routable
{
    public function __invoke(): array
    {
        Store::write('pageTitle', 'Terms & Conditions');
        return ['name' => 'Terms'];
    }
}