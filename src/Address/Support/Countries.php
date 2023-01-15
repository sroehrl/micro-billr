<?php

namespace App\Address\Support;

use Neoan\Routing\Interfaces\Routable;

class Countries implements Routable
{
    public array $countries;

    public function __invoke(): static
    {
        $this->countries = [
            'CA' => 'Canada',
            'US' => 'USA',
            'DE' => 'Germany',
            'CH' => 'Switzerland',
            'AT' => 'Austria'
        ];
        ksort($this->countries);
        return $this;
    }
}