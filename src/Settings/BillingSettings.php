<?php

namespace App\Settings;


use App\Auth\RequiresAdmin;
use App\Bill\CostCalculator;
use App\DocumentCreator\Create;
use Neoan\Enums\RequestMethod;
use Neoan\Request\Request;

class BillingSettings
{
    public function __invoke(RequiresAdmin $admin, &$feedback): array
    {
        $invoiceSettings = InvoiceSettingModel::retrieveOne(['companyId' => $admin->user->company()->id]);
        if(!$invoiceSettings){
            $invoiceSettings = new InvoiceSettingModel();
            $invoiceSettings->invoiceNumberFormat = 'Invoice-[[fullYear]]-[[number]]';
            $invoiceSettings->invoiceNumber = 1;
            $invoiceSettings->companyId = $admin->user->company()->id;
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
            ... $invoiceSettings->toArray(),
            'example' => CostCalculator::createInvoiceNumber($invoiceSettings),
        ];
    }
}