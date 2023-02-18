<?php

namespace App\Finances;

use App\Auth\RequiresAdmin;
use App\Bill\BillModel;
use Neoan\Database\Database;
use Neoan\Helper\DateHelper;
use Neoan\Routing\Attributes\Web;
use Neoan\Routing\Interfaces\Routable;

#[Web('/finances', 'Finances/views/overview.html')]
class FinancesOverview implements Routable
{
    public function __invoke(RequiresAdmin $admin): array
    {

        return [
            'chartData' => '/finances',
            'unpaidBills' => BillModel::retrieve(['^deletedAt', 'generatedDate' => '!null', '^paidAt', 'companyId' => $admin->user->companyId],['orderBy' => ['generatedDate','ASC']])
                ->each(fn(BillModel $bill) => $bill->withCustomer()->withProject())
                ->each(fn(BillModel $bill) => $bill->overdue = $bill->generatedDate->getTimeDifference(new DateHelper())->days)
                ->toArray()
        ];
    }

}