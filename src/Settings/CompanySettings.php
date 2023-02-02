<?php

namespace App\Settings;

use App\Address\Country;
use App\Company\CompanyModel;
use Neoan\Enums\RequestMethod;
use Neoan\Request\Request;

class CompanySettings
{


    public function __invoke(&$feedback): array
    {
        try{
            $company = CompanyModel::get(1);
        } catch (\Exception $e) {
            $company = new CompanyModel([
                'id' => 1,
                'name' => 'Acme Inc',
                'street' => '',
                'place' => '',
                'postalCode' => '',
                'state' => '',
                'country' => 'USA',
                'bankName' => '',
                'swiftBic' => '',
                'accountNumber' => '',
                'routingNumber' => '',
                'registry' => '',
            ]);
        }
        if(Request::getRequestMethod() === RequestMethod::POST) {
            [
                'name' => $company->name,
                'street' => $company->street,
                'place' => $company->place,
                'postalCode' => $company->postalCode,
                'state' => $company->state,
                'bankName' => $company->bankName,
                'accountName' => $company->accountName,
                'swiftBic' => $company->swiftBic,
                'accountNumber' => $company->accountNumber,
                'routingNumber' => $company->routingNumber,
                'registry' => $company->registry
            ] = Request::getInputs();
            $company->country = Country::from(Request::getInput('country'));
            $company->store();
            $feedback = 'Saved!';
        }

        return $company->toArray();
    }
}