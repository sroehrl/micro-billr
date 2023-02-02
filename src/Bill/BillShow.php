<?php

namespace App\Bill;

use App\Auth\Auth;
use App\Auth\BehindLogin;
use App\DocumentCreator\Create;
use App\Timesheet\TimesheetModel;
use Config\FormPost;
use Neoan\Enums\RequestMethod;
use Neoan\Helper\Setup;
use Neoan\Request\Request;
use Neoan\Routing\Attributes\Web;
use Neoan\Routing\Interfaces\Routable;

#[Web('/bill/:id', 'Bill/views/show.html', BehindLogin::class)]
#[FormPost('/bill/:id', 'Bill/views/show.html', BehindLogin::class)]
class BillShow implements Routable
{
    private BillModel $bill;
    private Setup $setup;
    private Create $documentCreator;

    public function __invoke(Auth $auth, Setup $setup, Create $documentCreator): array
    {
        $this->setup = $setup;
        $this->documentCreator = $documentCreator;
        $this->bill = BillModel::get(Request::getParameter('id'))->withCustomer()->withProject();
        $this->bill->project->withPerson();

        if(Request::getRequestMethod() === RequestMethod::POST) {
            $this->update();
        }

        $totalNet = CostCalculator::calculateTimesheetNet($this->bill->lineItems);

        $totalTax = CostCalculator::calculateTimesheetTaxAmount($this->bill->lineItems);

        $totalGross = $totalNet + $totalTax;

        $lineItems = $this->bill->lineItems->toArray();

        $company = $auth->user->company();
        $customer = $this->bill->customer();
        $downloadLink = '/documents/' . $customer->id . '/' . (isset($this->bill->createdAt) ?$this->bill->createdAt->stamp : '');
        $downloadLink .= '/invoice.pdf';
        return [
            'bill' => $this->bill->toArray(),
            'lineItems' => $lineItems,
            'customer' => $customer->toArray(),
            'customerAddress' => $customer->address()->printableAddress(),
            'company' => $company->toArray(),
            'companyAddress' => $company->printableAddress(),
            'totalNet' => $totalNet,
            'totalTax' => $totalTax,
            'totalGross' => $totalGross,
            'downloadLink' => $downloadLink
        ];
    }
    function update() :void
    {

        // steps:
        switch ($this->bill->billStatus){
            case BillStatus::PROCESSING:
                // taxes & overview
                if(Request::getInput('percent')){
                    foreach (Request::getInput('percent') as $id => $percent){
                        $item =TimesheetModel::get($id);
                        $item->taxPercent = $percent;
                        $item->store();
                    }
                }

                $this->bill->billStatus = BillStatus::GENERATED;
                $this->bill->generatedDate->set('now');
                $this->bill->store();
                $path = $this->setup->get('publicPath') . '/documents';
                foreach ([$this->bill->customerId,$this->bill->createdAt->stamp] as $part) {
                    $path .= '/' . $part;
                    @mkdir($path);
                }
                $fileName = $path . '/invoice.pdf';
                $this->documentCreator->generateInvoice($this->bill,'F', $fileName);
                break;
            case BillStatus::GENERATED:

                $this->bill->billStatus = BillStatus::SENT_OUT;
                break;

        }

    }
}