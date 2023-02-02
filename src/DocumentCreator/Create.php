<?php

namespace App\DocumentCreator;

use App\Auth\Auth;
use App\Bill\BillModel;
use App\Bill\CostCalculator;
use App\Customer\CustomerModel;
use App\Estimate\EstimateModel;
use App\Settings\InvoiceSettingModel;
use I18nTranslate\Translate;
use Neoan\Enums\TimePeriod;
use Neoan\Helper\DataNormalization;
use Neoan\Helper\Env;
use Neoan\Helper\Setup;
use Neoan\Model\Helper\DateTimeProperty;
use Neoan\Routing\Attributes\Get;
use Neoan\Routing\Interfaces\Routable;
use Neoan3\Apps\Template\Template;
use Spipu\Html2Pdf\Html2Pdf;

#[Get('/pdf')]
class Create implements Routable
{
    private Setup $setup;
    private Auth $auth;
    private string $outputMode = 'F';
    private string $templatePath = 'src/Bill/documents/bill-basic.html';

    public function __invoke(Setup $setup, Auth $auth): static
    {
        $this->setup = $setup;
        $this->auth = $auth;
        return $this;
    }
    public function setTemplatePath(string $path): static
    {
        $this->templatePath = $path;
        return $this;
    }
    public function setOutputMode(string $outputMode)
    {
        $this->outputMode = $outputMode;
    }

    public function generateEstimate(EstimateModel $estimate, string $pathOrName): string
    {
        $byMilestone = $estimate->itemsByMilestones();
        $totalNet = 0;
        $uniqueProducts = [];
        foreach ($byMilestone as $row){
            $totalNet += $row['net'];
            if(!array_key_exists($row['product']->name, $uniqueProducts)){
                $uniqueProducts[$row['product']->name] = $row['product'];
                $uniqueProducts[$row['product']->name]->description = '<p>' . nl2br($row['product']->description) . '</p>';
            }
        }

        $customer = CustomerModel::get($estimate->project()->customerId);
        $company = $this->auth->user->company();
        $validUntil = new DateTimeProperty();
        $validUntil->addTimePeriod(2, TimePeriod::WEEKS);

        $data = new DataNormalization([
            'privateKey' => $this->setup->get('libraryPath') . '/Auth/privKey.pem',
            'certificate' => $this->setup->get('libraryPath') . '/Auth/cert.pem',
            'validUntil' => $company->country->dateFormat($validUntil),
            'lineItems' => $byMilestone,
            'totalNet' => $totalNet,
            'customer' => $customer,
            'customerAddress' => $customer->address()->printableAddress,
            'company' => $company->toArray(),
            'companyAddress' => $company->printableAddress,
            'estimate' => $estimate->toArray(),
            'project' => $estimate->project()->title,
            'offerDate' => $company->country->dateFormat($estimate->updatedAt),
            'currency' => Env::get('currency','USD'),
            'uniqueProducts' => $uniqueProducts
        ]);
        return $this->generate($data, $pathOrName);
    }
    public function generateInvoice(BillModel $billModel, string $outputMode = 'I', ?string $storagePath = null): string
    {
        $name = $outputMode === 'F' ? $storagePath : $billModel->billNumber . '.pdf';
        $this->outputMode = $outputMode;

        $totalNet = CostCalculator::calculateTimesheetNet($billModel->lineItems);

        $totalTax = CostCalculator::calculateTimesheetTaxAmount($billModel->lineItems);

        $company = $this->auth->user->company();
        $settings = InvoiceSettingModel::retrieveOne(['companyId' => $company->id]);

        $lineItems = $billModel->lineItems->toArray();

        $data = new DataNormalization([
            'privateKey' => $this->setup->get('libraryPath') . '/Auth/privKey.pem',
            'certificate' => $this->setup->get('libraryPath') . '/Auth/cert.pem',
            'lineItems' => $lineItems,
            'totalNet' => $totalNet,
            'totalTax' => $totalTax,
            'totalGross' => $totalNet + $totalTax,
            'customer' => $billModel->customer()->toArray(),
            'customerAddress' => $billModel->customer()->address()->printableAddress,
            'company' => $company->toArray(),
            'companyAddress' => $company->printableAddress,
            'bill' => $billModel->toArray(),
            'invoiceDate' => $company->country->dateFormat($billModel->generatedDate),
            'currency' => Env::get('currency','USD'),
            'footer' => '<p>' . nl2br($settings->footer) . '</p>'
        ]);

        return $this->generate($data, $name);
    }

    private function generate(DataNormalization $data, string $name): string
    {
        $margins = [10,5,10,5];
        $html = new Html2Pdf('P', 'A4', 'en', true, 'UTF-8', $margins);
        $t = new Translate();
        $billHtml =  $t->translate(Template::embraceFromFile($this->templatePath, $data->toArray()));
        $html->writeHTML($billHtml);
        return $html->output($name, $this->outputMode);
    }

}