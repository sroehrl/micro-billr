<?php

namespace App\Timesheet;

use App\Auth\BehindLogin;
use Neoan\Request\Request;
use Neoan\Response\Response;
use Neoan\Routing\Attributes\Post;
use Neoan\Routing\Interfaces\Routable;

#[Post('/timesheet', BehindLogin::class)]
class TimesheetCreate implements Routable
{
    public function __invoke(): array
    {
        $newTimesheet = new TimesheetModel();
        [
            'projectId' => $newTimesheet->projectId,
            'milestoneId' => $newTimesheet->milestoneId,
            'productId' => $newTimesheet->productId,
            'hours' => $newTimesheet->hours,
        ] = Request::getInputs();
        $newTimesheet->workedAt->set(Request::getInput('workedAt'));
        $newTimesheet->store();
        Response::redirect($_SERVER['HTTP_REFERER']);
    }
}