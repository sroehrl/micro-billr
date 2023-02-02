<?php

namespace App\Bill;

use App\Product\BillingType;
use App\Settings\InvoiceSettingModel;
use App\Timesheet\TimesheetModel;
use Neoan\Database\Database;
use Neoan\Model\Collection;
use Neoan3\Apps\Template\Constants;
use Neoan3\Apps\Template\Template;

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
        return self::calculateTimesheetNet(TimesheetModel::retrieve(['projectId' => $projectId, '^billId', '^deletedAt']));

    }
    static function calculateTimesheetNet(Collection $timesheetCollection): float
    {
        self::$total = 0;
        $knownFlatrates = [];
        $timesheetCollection->each(function(TimesheetModel $timesheet) use(&$knownFlatrates){
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
    static function calculateTimesheetTaxAmount(Collection $timesheetCollection) : float
    {
        $totalTax = 0;
        $knownFlatrates = [];
        $timesheetCollection->each(function(TimesheetModel $timesheet) use(&$totalTax, &$knownFlatrates){
            if(isset($timesheet->taxPercent) && $timesheet->taxPercent > 0) {
                $product = $timesheet->product();
                if($product->billingType === BillingType::FLATRATE && !in_array($product->id, $knownFlatrates)){
                    $totalTax += ($product->price * $timesheet->taxPercent / 100);
                    $knownFlatrates[] = $product->id;
                } elseif($product->billingType !== BillingType::FLATRATE) {
                    $totalTax += ( $product->price * $timesheet->hours * $timesheet->taxPercent / 100);
                }
            }


        });
        return $totalTax;
    }
    static function createInvoiceNumber(InvoiceSettingModel $invoiceSettings): string
    {
        $data = [
            'year' => date('y'),
            'fullYear' => date('Y'),
            'number' => str_pad($invoiceSettings->invoiceNumber, $invoiceSettings->invoiceNumberPadding, '0', STR_PAD_LEFT),
            'month' => date('m'),
            'quarter' => ceil((int)date('m')/3)
        ];
        $oldDelimiter = Constants::getDelimiter();
        Constants::setDelimiter('\[\[','\]\]');
        $result = Template::embrace('<p>' .$invoiceSettings->invoiceNumberFormat .'</p>', $data);
        Constants::setDelimiter($oldDelimiter[0], $oldDelimiter[1]);
        return substr($result,3,-4);
    }
}