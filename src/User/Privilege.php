<?php

namespace App\User;

enum Privilege: string
{
    case ADMIN = 'admin';
    case EMPLOYEE = 'employee';
    case CUSTOMER = 'customer';
}
