<?php

namespace App\Person;

use App\Auth\BehindLogin;
use App\Auth\Permission\PersonPermission;
use Config\FormPost;
use Neoan\Enums\RequestMethod;
use Neoan\Request\Request;
use Neoan\Routing\Attributes\Web;
use Neoan\Routing\Interfaces\Routable;

#[Web('/person/:personId', '/Person/views/person.html', BehindLogin::class, PersonPermission::class)]
#[FormPost('/person/:personId', '/Person/views/person.html', BehindLogin::class, PersonPermission::class)]
class PersonShow implements Routable
{
    public function __invoke(): array
    {
        $person = PersonModel::get(Request::getParameter('personId'))->withCustomer();
        if(RequestMethod::POST === Request::getRequestMethod()){
            $person->gender = Gender::from(Request::getInput('gender'));
            [
                'firstName' => $person->firstName,
                'lastName' => $person->firstLast,
                'middleName' => $person->middleName,
                'email' => $person->email,
                'phone' => $person->phone,
            ] = Request::getInputs();
            $person->store();
        }
        return $person->toArray();
    }
}