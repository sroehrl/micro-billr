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
        $sortDirection = 'ascending';
        if(str_starts_with($sort, '-')){
            $sort = substr($sort, 1);
            $sortDirection = 'descending';
        }
        $filter = Request::getQuery('filter') ? ['status' => Request::getQuery('filter')] : [];
        $pagination =  ProjectModel::paginate($page, 30)
            ->where($filter)
            ->{$sortDirection}($sort)
            ->get();
        $pagination['collection']->each(function (ProjectModel $item){
            $item->projectStatus = $item->status->value;
        });

        return $pagination;
    }
}