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
//        $timeSheets = TimesheetModel::retrieve(['^deletedAt', 'milestoneId' => $milestone->id],['orderBy'=> ['workedAt', 'DESC']]);

        $sql = '
        SELECT sum(t.hours) as hours, p.name, t.workedAt
            FROM timesheet_model t
            JOIN product_model p ON p.id = t.productId
            
            WHERE t.milestoneId = {{mId}} AND
                  t.deletedAt IS NULL
            GROUP BY t.workedAt, t.productId
        ';
        $actuals = [];
        $results = Database::raw($sql, ['mId' => $milestone->id]);
        $associated = [];
        $knownProducts = [];
        foreach ($results as $i => $row){
            if(!in_array($row['name'], $knownProducts)) {
                $knownProducts[] = $row['name'];
            }
            if(!isset($associated[$row['workedAt']])){
                $associated[$row['workedAt']] = [
                    $row['name'] => [
                        'label' => $row['name'],
                        'hours' => (int) $row['hours']
                    ],
                    'total' => 0,
                    'date' => $row['workedAt']
                ];
                $actuals[] = (count($actuals) === 0 ? 0 : $actuals[count($actuals)-1]) + $row['hours'];
            } elseif(isset($associated[$row['workedAt']][$row['name']])  ) {
                $associated[$row['workedAt']][$row['name']]['hours'] += (int) $row['hours'];
            } else {
                $associated[$row['workedAt']][$row['name']] = [
                    'label' => $row['name'],
                    'hours' => (int) $row['hours']
                ];
            }
            $associated[$row['workedAt']]['total'] += (int) $row['hours'];
        }
        $data = [
            'labels' => array_keys($associated),
            'datasets' => [
                ['label' => 'Estimate', 'data' => array_fill(0, count($associated), $milestone->estimatedHours), 'type' => 'line'],
                ['label' => 'Worked on date', 'data' => array_map(fn ($row) => $row['total']/100, $associated)],
                ['label' => 'Accumulated work hours', 'data' => array_map(fn($actual) => $actual/100,$actuals), 'type' => 'line'],
            ]
        ];
        $originalCount = count($data['datasets']);
        foreach ($knownProducts as $knownProduct) {
            $data['datasets'][] = [
                'label' => $knownProduct,
                'data' => array_fill(0, count($associated), 0),
                'type' => 'line',
                'hidden' => true,
                'fill' => true
            ];
        }
        $counter = 0;
        foreach ($associated as $i => $stackedItem){
            foreach ($stackedItem as $name => $item) {
                if(!in_array($name,['total', 'date'])){
                    $data['datasets'][array_search($name, $knownProducts) + $originalCount]['data'][$counter] = $item['hours'] / 100;

                }

            }
            $counter++;
        }
        return $data;
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