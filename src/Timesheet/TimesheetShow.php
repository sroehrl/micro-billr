<?php

namespace App\Timesheet;

use App\Auth\BehindLogin;
use App\Note\NoteModel;
use Neoan\Request\Request;
use Neoan\Routing\Attributes\Web;
use Neoan\Routing\Interfaces\Routable;
use Neoan3\Apps\Session;

#[Web('/timesheet/:hash', 'Timesheet/views/show.html', BehindLogin::class)]
class TimesheetShow implements Routable
{
    public function __invoke(): array
    {
        $timesheet = $this->retrieve();

        return [
            'timesheet' => $timesheet->toArray(),
            'loggedIn' => Session::isLoggedIn(),
            'noteType' => 'timesheet',
            'relationId' => $timesheet->id,
            'notes' => NoteModel::retrieve(['^deletedAt', 'relationId' => $timesheet->id, 'noteType' => 'timesheet'])
        ];
    }
    private function retrieve(): TimesheetModel
    {
        $hashOrId = Request::getParameter('hash');
        if(strlen($hashOrId) === 36){
            return TimesheetModel::retrieveOne(['hash' => $hashOrId])->withProduct()->withMilestone()->withProject();
        } else {
            // you need to be logged in
            Session::restrict();
            return TimesheetModel::get($hashOrId)->withProduct()->withMilestone()->withProject();
        }
    }
}