<?php

namespace App\Customer;

use App\Auth\Auth;
use App\Auth\BehindLogin;
use Config\FormPost;
use Neoan\Enums\RequestMethod;
use Neoan\Helper\Str;
use Neoan\Request\Request;
use Neoan\Response\Response;
use Neoan\Routing\Attributes\Web;
use Neoan\Routing\Interfaces\Routable;
use Neoan3\Apps\Session;
use Ramsey\Uuid\Uuid;

#[
    Web('/customer/new', 'Customer/views/create.html', BehindLogin::class),
    FormPost('/customer/new', 'Customer/views/create.html', BehindLogin::class)
]
class CustomerCreate implements Routable
{
    public function __invoke(Auth $auth): array
    {
        if(Request::getRequestMethod() === RequestMethod::POST) {
            $newCustomer = new CustomerModel([
                ...Request::getInputs(),
                'companyId' => $auth->user->companyId
            ]);
            try{
                $newCustomer->store();
                Session::addToSession(['comingFrom' => 'customer']);
                Response::redirect('/customer/' . $newCustomer->id);
            } catch (\Exception $e) {

            }

        }
        return ['customerNumber' => Str::randomAlphaNumeric(6)];
    }
}