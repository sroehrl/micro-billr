<?php

namespace App\Product;

enum BillingType: string
{
    case HOURLY = 'hourly';
    case FLATRATE = 'flatrate';
}
