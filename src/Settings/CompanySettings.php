<?php

namespace App\Settings;

use App\Address\Country;
use App\Auth\BehindLogin;
use App\Auth\RequiresAdmin;
use App\Company\CompanyModel;
use Config\FormPost;
use Neoan\Enums\RequestMethod;
use Neoan\Request\Request;
use Neoan\Routing\Attributes\Web;
use Neoan\Routing\Interfaces\Routable;
use Neoan\Store\Store;

#[Web('/settings/company', 'Settings/views/settings.html', BehindLogin::class)]
#[FormPost('/settings/company', 'Settings/views/settings.html', BehindLogin::class)]
class CompanySettings implements Routable
{


    public function __invoke(RequiresAdmin $admin): array
    {
        Store::write('pageTitle', 'Company settings');
        $feedback = '';
        try{
            $company = CompanyModel::get($admin->user->companyId);
        } catch (\Exception $e) {
            $company = new CompanyModel([
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
                'color' => '#2ed6a4'
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
                'phoneNumber' => $company->phoneNumber,
                'website' => $company->website,
                'color' => $company->color,
                'registry' => $company->registry,
                'paypal' => $company->paypal
            ] = Request::getInputs();
            $company->country = Country::from(Request::getInput('country'));
            $company->store();
            $feedback = ' Company and bank data saved!';
        }
        $countries = [];
        foreach (Country::cases() as $case){
            $countries[$case->value] = $case->value;
        }
        return [
            'tab' => 'company',
            'data' => $company->toArray(),
            'feedback' => $feedback,
            'countries' => $countries
        ];
    }
}