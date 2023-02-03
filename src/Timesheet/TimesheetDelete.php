<?php

namespace App\Timesheet;

use App\Auth\BehindLogin;
use Neoan\Request\Request;
use Neoan\Response\Response;
use Neoan\Routing\Attributes\Delete;
use Neoan\Routing\Interfaces\Routable;

#[Delete('/api/timesheet/:id', BehindLogin::class)]
class TimesheetDelete implements Routable
{
    public function __invoke(): array
    {
        $timesheet = TimesheetModel::get(Request::getParameter('id'));


        if(empty($timesheet->billId)){
            $timesheet->deletedAt->set('now');
            $timesheet->store();
            return ['item' => 'deleted', 't' => $timesheet];
        }
        Response::setStatusCode(402);
        return ['item' => 'can\'t delete'];
    }
}