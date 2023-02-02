<?php

namespace App\Product;

enum BillingType: string
{
    case HOURLY = 'hourly';
    case FLATRATE = 'flatrate';
    case MONTHLY = 'monthly';
    case YEARLY = 'yearly';
}
