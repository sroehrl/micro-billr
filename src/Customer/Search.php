<?php

namespace App\Customer;

use App\Auth\BehindLogin;
use Neoan\Request\Request;
use Neoan\Routing\Attributes\Get;
use Neoan\Routing\Interfaces\Routable;

#[Get('/api/customer', BehindLogin::class)]
class Search implements Routable
{
    public function __invoke(): \Neoan\Model\Collection
    {
        return CustomerModel::retrieve([
            'title' => '%' . Request::getQuery('search') . '%'
        ]);
    }
}