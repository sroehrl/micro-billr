<?php

namespace App\Home;

use App\Auth\BehindLogin;
use App\Calendar\CalendarEvents;
use App\Project\ProjectModel;
use App\Project\ProjectStatus;
use Neoan\Routing\Attributes\Web;
use Neoan\Routing\Interfaces\Routable;
use Neoan\Store\Store;

#[Web('/', 'Home/home.html', BehindLogin::class)]
class Home implements Routable
{
    public function __invoke(): array
    {
        Store::write('pageTitle', 'Welcome');
        return [
            'openProjects' => ProjectModel::retrieve([
                '^deletedAt',
                'status' => ProjectStatus::IN_PROGRESS->value
            ])->count(),
            'plannedProjects' => ProjectModel::retrieve([
                '^deletedAt',
                'status' => ProjectStatus::PLANNED->value
            ])->count(),
            'events' => json_encode(CalendarEvents::allEvents())
        ];
    }
}