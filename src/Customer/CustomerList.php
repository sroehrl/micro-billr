<?php

namespace App\Customer;

use App\Auth\BehindLogin;
use Neoan\Request\Request;
use Neoan\Routing\Attributes\Web;
use Neoan\Routing\Interfaces\Routable;
use Neoan\Store\Store;

#[Web('/customer', 'Customer/views/list.html', BehindLogin::class)]
class CustomerList implements Routable
{
    public function __invoke(): array
    {
        $page = Request::getQuery('page') ?? 1;
        $sort = Request::getQuery('sort') ?? 'title';
        $filter = Request::getQuery('filter') ?? '';

        $sortDirection = 'ascending';
        if(str_starts_with($sort, '-')){
            $sort = substr($sort, 1);
            $sortDirection = 'descending';
        }
        return CustomerModel::paginate($page, 30)
            ->{$sortDirection}($sort)
            ->where(['title' => $filter . '%'])
            ->get();
    }
}