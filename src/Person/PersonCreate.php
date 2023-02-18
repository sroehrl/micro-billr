<?php

namespace App\Person;

use App\Address\AddressModel;
use App\Auth\Auth;
use App\Auth\BehindLogin;
use App\Customer\CustomerModel;
use Config\FormPost;
use Neoan\Database\Database;
use Neoan\Enums\RequestMethod;
use Neoan\Request\Request;
use Neoan\Response\Response;
use Neoan\Routing\Attributes\Web;
use Neoan\Routing\Interfaces\Routable;
use Neoan3\Apps\Session;

#[
    Web('/person/new/:customerId*', 'Person/views/create.html', BehindLogin::class),
    FormPost('/person/new', 'Person/views/create.html', BehindLogin::class)
]
class PersonCreate implements Routable
{
    public function __invoke(Auth $auth): array
    {
        if(Request::getRequestMethod() === RequestMethod::POST) {
            $customerData = explode(' | ', Request::getInput('customer'));
            $newPerson = new PersonModel(Request::getInputs());
            $newPerson->customerId = CustomerModel::retrieveOne([
                'customerNumber' => $customerData[0],
                'title' => $customerData[1]
            ])->id;
            $newPerson->gender = Gender::tryFrom(Request::getInput('gender'));
            try{
                $newPerson->store();
                Session::addToSession(['comingFrom' => 'person']);
                Response::redirect('/customer/' . $newPerson->customerId);
            } catch (\Exception $e) {

            }

        }

        $customer = '';
        if(Request::getParameter('customerId')){
            $preselectedCustomer = CustomerModel::get(Request::getParameter('customerId'));
            $customer = $preselectedCustomer->customerNumber . ' | ' . $preselectedCustomer->title;
        }
        return [
            'customer' => $customer,
            'customers' => Database::easy('customer_model.customerNumber customer_model.title', [
                '^deletedAt',
                'companyId' => $auth->user->companyId
            ])
        ];
    }
}