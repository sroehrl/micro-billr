<?php

namespace App\Customer;

use App\Address\AddressModel;
use App\Address\Country;
use App\Address\Support\Countries;
use App\Auth\BehindLogin;
use Neoan\Enums\GenericEvent;
use Neoan\Enums\RequestMethod;
use Neoan\Event\Event;
use Neoan\Helper\Terminate;
use Neoan\Request\Request;
use Neoan\Response\Response;
use Neoan\Routing\Attributes\Web;
use Neoan\Routing\Interfaces\Routable;
use Neoan3\Apps\Session;

#[Web('/customer/:id', 'Customer/views/show.html', BehindLogin::class)]
class CustomerShow implements Routable
{
    public function __invoke(Countries $countries): array
    {

        try{
            $customer = CustomerModel::get(Request::getParameter('id'));
            if(isset($customer->addressId)){
                $customer->withAddress();
            } else {
                $customer->addressId = 0;
            }

        } catch (\Exception $e) {
            Response::redirect('/customer');
        }

        $countries = [];
        foreach (Country::cases() as $case){
            $countries[$case->value] = $case->value;
        }

        return [
            ...$customer->toArray(),
            'countries' => $countries,
            'feedback' => $this->comingFrom()
        ];

    }

    private function comingFrom(): string
    {
        $from = $_SESSION['comingFrom'] ?? 'generic';
        Session::addToSession(['comingFrom' => 'generic']);
        return match($from){
            'address' => 'Address saved!',
            'customer' => 'Customer data saved!',
            default => ''
        };
    }
}