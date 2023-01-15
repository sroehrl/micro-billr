<?php

namespace App\Settings;


class BillingSettings
{
    public function __invoke(&$feedback): array
    {
        return ['name' => 'BillingSettings'];
    }
}