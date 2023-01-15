<?php

namespace App\Settings;

use App\Company\CompanyModel;
use Neoan\Enums\RequestMethod;
use Neoan\Request\Request;

class CompanySettings
{


    public function __invoke(&$feedback): CompanyModel
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
                'country' => 'US',
                'bankName' => '',
                'swiftBic' => '',
                'accountNumber' => '',
                'routingNumber' => '',
            ]);
        }
        if(Request::getRequestMethod() === RequestMethod::POST) {
            [
                'name' => $company->name,
                'street' => $company->street,
                'place' => $company->place,
                'postalCode' => $company->postalCode,
                'state' => $company->state,
                'country' => $company->country,
            ] = Request::getInputs();
            $company->store();
            $feedback = 'Saved!';
        }

        return $company;
    }
}