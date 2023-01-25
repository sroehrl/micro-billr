<?php

namespace App\Person;

use App\Auth\BehindLogin;
use Neoan\Database\Database;
use Neoan\Request\Request;
use Neoan\Routing\Attributes\Get;
use Neoan\Routing\Interfaces\Routable;

#[Get('/api/person', BehindLogin::class)]
class Search implements Routable
{
    public function __invoke(): array
    {
        $sql = 'SELECT id, CONCAT_WS(" ", firstName, middleName, lastName) as fullName FROM person_model WHERE deletedAt IS NULL AND customerId = {{customerId}} AND ';
        $sql .= 'CONCAT_WS(" ", firstName, lastName) LIKE {{search}}';


        return Database::raw($sql, [
            'customerId' => Request::getQuery('customerId'),
            'search' => '%' . Request::getQuery('search') . '%'
        ]);



    }
}