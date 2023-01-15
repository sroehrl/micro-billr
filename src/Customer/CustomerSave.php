<?php

namespace App\Customer;

use App\Auth\BehindLogin;
use Config\FormPost;
use Neoan\Request\Request;
use Neoan\Response\Response;
use Neoan\Routing\Interfaces\Routable;
use Neoan3\Apps\Session;

#[FormPost('/customer/:id', null, BehindLogin::class)]
class CustomerSave implements Routable
{
    public function __invoke(): array
    {
        $customer = CustomerModel::get(Request::getParameter('id'));
        foreach (Request::getInputs() as $key => $value) {
            if(property_exists($customer, $key)){
                $customer->$key = $value;
            }
        }
        $customer->store();
        Session::addToSession(['comingFrom' => 'customer']);
        Response::redirect('/customer/'. Request::getParameter('id'));
        return ['name' => 'CustomerSave'];
    }
}