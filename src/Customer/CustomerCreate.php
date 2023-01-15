<?php

namespace App\Customer;

use App\Auth\BehindLogin;
use Config\FormPost;
use Neoan\Enums\RequestMethod;
use Neoan\Request\Request;
use Neoan\Response\Response;
use Neoan\Routing\Attributes\Web;
use Neoan\Routing\Interfaces\Routable;
use Neoan3\Apps\Session;

#[
    Web('/customer/new', 'Customer/views/create.html', BehindLogin::class),
    FormPost('/customer/new', 'Customer/views/create.html', BehindLogin::class)
]
class CustomerCreate implements Routable
{
    public function __invoke(): array
    {
        if(Request::getRequestMethod() === RequestMethod::POST) {
            $newCustomer = new CustomerModel(Request::getInputs());
            try{
                $newCustomer->store();
                Session::addToSession(['comingFrom' => 'customer']);
                Response::redirect('/customer/' . $newCustomer->id);
            } catch (\Exception $e) {

            }

        }
        return ['customerNumber' => 'suggest'];
    }
}