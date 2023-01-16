<?php

namespace App\Project;

use App\Auth\BehindLogin;
use Neoan\Routing\Attributes\Web;
use Neoan\Routing\Interfaces\Routable;

#[Web('/project/new', 'Project/views/create.html', BehindLogin::class)]
class ProjectCreate implements Routable
{
    public function __invoke(): array
    {

        return ['name' => 'ProjectCreate'];
    }
}