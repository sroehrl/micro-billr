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
        $generatedSQL = '
        SELECT SUM(totalNet) as monthNet,
               DATE_FORMAT(generatedDate,"%Y-%m") as month
        FROM bill_model WHERE
                            deletedAt IS NULL AND
                            generatedDate IS NOT NULL 
        GROUP BY month
        ';
        $paidSQL = '
        SELECT SUM(totalNet) as monthNet,
               DATE_FORMAT(generatedDate,"%Y-%m") as month
        FROM bill_model WHERE
                            deletedAt IS NULL AND
                            generatedDate IS NOT NULL AND
                            paidAt IS NOT NULL
        GROUP BY month
        ';
        $unpaidSQL = '
        SELECT SUM(totalNet) as monthNet,
               DATE_FORMAT(generatedDate,"%Y-%m") as month
        FROM bill_model WHERE
                            deletedAt IS NULL AND
                            generatedDate IS NOT NULL AND
                            paidAt IS NULL
        GROUP BY month
        ';
        $unbilledSQL = '
        
        ';


        $months = [];
        $monthData = $this->arrangeData($generatedSQL);
        $paidData = $this->arrangeData($paidSQL);
        $unpaidData = $this->arrangeData($unpaidSQL);
        for($i = 6; $i >= 0; $i--){
            $currentMonth = strtotime('-' . $i . ' months');
            $months[] = date('m/Y', $currentMonth);
        }



        $chartData = [
            'labels' => $months,
            'datasets' => [
                ['label' => 'Net invoiced in month', 'data' => $monthData, 'backgroundColor' => 'rgba(39, 179, 225, 0.6)'],
                ['label' => 'Net paid in month', 'data' => $paidData, 'type' => 'line', 'backgroundColor' => '#1be1db', 'borderColor' => '#1be1db'],
                ['label' => 'Outstanding', 'data' => $unpaidData, 'type' => 'line', 'backgroundColor' => 'red', 'borderColor' => 'red'],
            ]
        ];
        return [
            'chartData' => json_encode($chartData),
            'unpaidBills' => BillModel::retrieve(['^deletedAt', 'generatedDate' => '!null', '^paidAt'],['orderBy' => ['generatedDate','ASC']])
                ->each(fn(BillModel $bill) => $bill->withCustomer()->withProject())
                ->each(fn(BillModel $bill) => $bill->overdue = $bill->generatedDate->getTimeDifference(new DateHelper())->days)
                ->toArray()
        ];
    }
    private function arrangeData($sql)
    {
        $dataArray = [];
        $result = Database::raw($sql);
        for($i = 6; $i >= 0; $i--) {
            $currentMonth = strtotime('-' . $i . ' months');
            $index = array_search(date('Y-m', $currentMonth), array_column($result, 'month'));
            $dataArray[] = $index !== false ? $result[$index]['monthNet'] / 100 : 0;
        }
       return $dataArray;
    }
}