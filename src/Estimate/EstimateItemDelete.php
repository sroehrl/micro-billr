<?php

namespace App\Estimate;

use App\Auth\BehindLogin;
use Neoan\Request\Request;
use Neoan\Routing\Attributes\Delete;
use Neoan\Routing\Interfaces\Routable;

#[Delete('/api/estimate-item/:id', BehindLogin::class)]
class EstimateItemDelete implements Routable
{
    public function __invoke(): array
    {
        $item = EstimateItemModel::get(Request::getParameter('id'));
        $item->delete(true);
        return [
            'item' => 'deleted'
        ];
    }
}