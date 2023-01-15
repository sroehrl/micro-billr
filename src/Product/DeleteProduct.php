<?php

namespace App\Product;

use App\Auth\RequiresAdmin;
use Neoan\Request\Request;
use Neoan\Routing\Attributes\Delete;
use Neoan\Routing\Interfaces\Routable;

#[Delete('/api/product/:id', RequiresAdmin::class)]
class DeleteProduct implements Routable
{
    public function __invoke(): array
    {
        ProductModel::get(Request::getParameter('id'))->delete();
        return ['name' => 'DeleteProduct'];
    }
}