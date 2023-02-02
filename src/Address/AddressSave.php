<?php

namespace App\Address;

use App\Auth\BehindLogin;
use App\Customer\CustomerModel;
use Config\FormPost;
use Neoan\Request\Request;
use Neoan\Response\Response;
use Neoan\Routing\Interfaces\Routable;
use Neoan3\Apps\Session;

#[FormPost('/address/:id', null, BehindLogin::class)]
class AddressSave implements Routable
{
    public function __invoke(): array
    {
        $id = Request::getParameter('id');
        Session::addToSession(['comingFrom' => 'address']);
        $address = $id == 0 ? new AddressModel() : AddressModel::get($id);
        [
            'street' => $address->street,
            'state' => $address->state,
            'place' => $address->place,
            'postalCode' => $address->postalCode,
        ] = Request::getInputs();
        $address->country = Country::tryFrom(Request::getInput('country'));
        $address->store();

        // associate
        if($customerId =Request::getInput('customerId')){
            $customer = CustomerModel::get($customerId);
            $customer->addressId = $address->id;
            $customer->store();
            Response::redirect('/customer/' . $customerId);
        }


        return ['name' => 'AddressSave'];
    }
}