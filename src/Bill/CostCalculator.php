<?php

namespace App\Bill;

use App\Product\BillingType;
use App\Timesheet\TimesheetModel;
use Neoan\Database\Database;

class CostCalculator
{
    private static float $total = 0;
    static function unbilledHoursOnProject(int $projectId): float
    {
        $result = 0;
        $sql = 'SELECT SUM(hours) as outstanding FROM timesheet_model WHERE deletedAt IS NULL AND billId IS NULL AND projectId = '. $projectId;
        $q = Database::raw($sql);
        if(!empty($q)){
            $result = $q[0]['outstanding'] / 100;
        }
        return $result;
    }
    static function unbilledHoursOnMilestone(int $milestoneId): float
    {
        $result = 0;
        $sql = 'SELECT SUM(hours) as outstanding FROM timesheet_model WHERE deletedAt IS NULL AND billId IS NULL AND milestoneId = '. $milestoneId;
        $q = Database::raw($sql);
        if(!empty($q)){
            $result = $q[0]['outstanding'] / 100;
        }
        return $result;
    }

    static function unbilledNetOnProject(int $projectId): float
    {
        self::$total = 0;
        $knownFlatrates = [];
        TimesheetModel::retrieve(['projectId' => $projectId, '^billId', '^deletedAt'])->each(function(TimesheetModel $timesheet)use(&$knownFlatrates){
            $product = $timesheet->product();
            if($product->billingType === BillingType::FLATRATE){
                self::$total += in_array($product->id, $knownFlatrates) ? 0 : $product->price;
                $knownFlatrates[] = $product->id;
            } else {
                self::$total += ( $product->price * $timesheet->hours);
            }
        });
        return self::$total;
    }
}