<?php

namespace App\Project;

use App\Auth\Auth;
use App\Auth\BehindLogin;
use App\Customer\CustomerModel;
use App\Person\PersonModel;
use App\User\Privilege;
use Neoan\Request\Request;
use Neoan\Routing\Attributes\Web;
use Neoan\Routing\Interfaces\Routable;

#[Web('/project', 'Project/views/list.html', BehindLogin::class)]
class ProjectList implements Routable
{
    public function __invoke(Auth $auth): array
    {
        $page = Request::getQuery('page') ?? 1;
        $sort = Request::getQuery('sort') ?? 'title';
        $sortDirection = 'ascending';
        if(str_starts_with($sort, '-')){
            $sort = substr($sort, 1);
            $sortDirection = 'descending';
        }
        $baseFilter = ['companyId' => $auth->user->companyId];
        if($auth->user->privilege === Privilege::CUSTOMER){
            $baseFilter['customerId'] = $auth->user->person()->customerId;

        }
        $filter = Request::getQuery('filter') ? ['status' => Request::getQuery('filter')] : [];
        $pagination =  ProjectModel::paginate($page, 30)
            ->where([...$filter, ...$baseFilter])
            ->{$sortDirection}($sort)
            ->get();
        $pagination['collection']->each(function (ProjectModel $item){
            $item->projectStatus = $item->status->value;
        });

        return $pagination;
    }
}