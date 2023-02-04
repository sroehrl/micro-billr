<?php

namespace App\Chart;

use App\Auth\BehindLogin;
use App\Milestone\MilestoneModel;
use App\Timesheet\TimesheetModel;
use Neoan\Database\Database;
use Neoan\Request\Request;
use Neoan\Routing\Attributes\Get;
use Neoan\Routing\Interfaces\Routable;

#[Get('/api/chart/:entity/:id*', BehindLogin::class)]
class ChartData implements Routable
{
    public function __invoke(): array
    {
        switch (Request::getParameter('entity')) {
            case 'milestone':
                return Request::getParameter('id') ? $this->singleMilestone() : $this->allMilestones();
                break;
            case 'finances':
                return $this->finances();
                break;
        }
        return ['name' => 'ChartData'];
    }
    private function singleMilestone(): array
    {
        $milestone = MilestoneModel::get(Request::getParameter('id'));
        $timeSheets = TimesheetModel::retrieve(['^deletedAt', 'milestoneId' => $milestone->id],['orderBy'=> ['workedAt', 'DESC']]);
        return [
            'labels' => array_map(fn($timeSheet) => $timeSheet['productName'], $timeSheets->toArray()),
            'datasets' => [
                ['label' => 'Estimate', 'data' => array_fill(0, $timeSheets->count(), $milestone->estimatedHours), 'type' => 'line'],
                ['label' => 'Product', 'data' => array_map(fn ($timeSheet) => $timeSheet['hours'], $timeSheets->toArray())],
                ['label' => 'Actual', 'data' => array_fill(0, $timeSheets->count(), $milestone->actualHours), $timeSheets->toArray(), 'type' => 'line'],
            ]
        ];
    }
    private function allMilestones(): array
    {
        return [];
    }
    private function finances(): array
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



        $months = [];
        $monthData = $this->arrangeData($generatedSQL);
        $paidData = $this->arrangeData($paidSQL);
        $unpaidData = $this->arrangeData($unpaidSQL);
        for($i = 6; $i >= 0; $i--){
            $currentMonth = strtotime('-' . $i . ' months');
            $months[] = date('m/Y', $currentMonth);
        }



        return [
            'labels' => $months,
            'datasets' => [
                ['label' => 'Net invoiced in month', 'data' => $monthData, 'backgroundColor' => 'rgba(39, 179, 225, 0.6)'],
                ['label' => 'Net paid in month', 'data' => $paidData, 'type' => 'line', 'backgroundColor' => '#1be1db', 'borderColor' => '#1be1db'],
                ['label' => 'Outstanding', 'data' => $unpaidData, 'type' => 'line', 'backgroundColor' => 'red', 'borderColor' => 'red'],
            ]
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