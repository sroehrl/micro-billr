<?php

namespace App\Project;

use App\Auth\BehindLogin;
use Neoan\Request\Request;
use Neoan\Routing\Attributes\Web;
use Neoan\Routing\Interfaces\Routable;

#[Web('/project', 'Project/views/list.html', BehindLogin::class)]
class ProjectList implements Routable
{
    public function __invoke(): array
    {
        $page = Request::getQuery('page') ?? 1;
        $sort = Request::getQuery('sort') ?? 'title';
        return ProjectModel::paginate($page, 30)
            ->ascending($sort)
            ->get();
    }
}