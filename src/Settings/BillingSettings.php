<?php

namespace App\Settings;


use App\Auth\BehindLogin;
use App\Auth\RequiresAdmin;
use App\Bill\CostCalculator;
use App\DocumentCreator\Create;
use Config\FormPost;
use Neoan\Enums\RequestMethod;
use Neoan\Request\Request;
use Neoan\Routing\Attributes\Web;
use Neoan\Routing\Interfaces\Routable;
use Neoan\Store\Store;

#[Web('/settings/billing', 'Settings/views/settings.html', BehindLogin::class)]
#[FormPost('/settings/billing', 'Settings/views/settings.html', BehindLogin::class)]
class BillingSettings implements Routable
{
    public function __invoke(RequiresAdmin $admin): array
    {
        Store::write('pageTitle', 'Billing/Invoicing settings');
        $invoiceSettings = InvoiceSettingModel::retrieveOne(['companyId' => $admin->user->companyId]);
        $feedback = '';
        if(!$invoiceSettings){
            $invoiceSettings = new InvoiceSettingModel();
            $invoiceSettings->invoiceNumberFormat = 'Invoice-[[fullYear]]-[[number]]';
            $invoiceSettings->invoiceNumber = 1;
            $invoiceSettings->companyId = $admin->user->companyId;
            $invoiceSettings->invoiceNumberPadding = 4;
            $invoiceSettings->store();
        }
        if(Request::getRequestMethod() === RequestMethod::POST) {
            [
                'footer' => $invoiceSettings->footer,
                'invoiceNumber' => $invoiceSettings->invoiceNumber,
                'invoiceNumberPadding' => $invoiceSettings->invoiceNumberPadding,
                'invoiceNumberFormat' => $invoiceSettings->invoiceNumberFormat
            ] = Request::getInputs();
            $invoiceSettings->store();
            $feedback = 'Invoice settings saved';
        }

        return [
            'tab' => 'billing',
            'data' => [
                ... $invoiceSettings->toArray(),
                'example' => CostCalculator::createInvoiceNumber($invoiceSettings),
            ],
            'feedback' => $feedback,
        ];
    }
}