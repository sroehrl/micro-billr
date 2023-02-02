<?php

namespace App\Settings;

use App\Auth\RequiresAdmin;
use App\Bill\BillModel;
use App\Bill\BillStatus;
use App\Bill\CostCalculator;
use App\DocumentCreator\Create;
use App\Timesheet\TimesheetModel;
use Neoan\Routing\Attributes\Get;
use Neoan\Routing\Interfaces\Routable;

#[Get('/settings/billing/example', RequiresAdmin::class)]
class BillExample implements Routable
{
    public function __invoke(RequiresAdmin $admin, Create $docCreator): array
    {
        $invoiceSettings = InvoiceSettingModel::retrieveOne(['companyId' => $admin->user->company()->id]);


        $bill = new BillModel();
        $bill->id = 1;
        $bill->billNumber = CostCalculator::createInvoiceNumber($invoiceSettings);
        $bill->billStatus = BillStatus::GENERATED;
        $bill->generatedDate->set('now');
        $bill->customerId = 1;
        $bill->sentDate->set('now');
        $docCreator->generateInvoice($bill, 'I');
    }
}