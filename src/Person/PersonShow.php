<?php

namespace App\Person;

use App\Auth\Auth;
use App\Auth\BehindLogin;
use App\Auth\Permission\PersonPermission;
use App\Invitation\InvitationModel;
use App\User\Privilege;
use App\User\UserModel;
use Config\FormPost;
use Neoan\Enums\RequestMethod;
use Neoan\NeoanApp;
use Neoan\Request\Request;
use Neoan\Routing\Attributes\Web;
use Neoan\Routing\Interfaces\Routable;

#[Web('/person/:personId', '/Person/views/person.html', BehindLogin::class, PersonPermission::class)]
#[FormPost('/person/:personId', '/Person/views/person.html', BehindLogin::class, PersonPermission::class)]
class PersonShow implements Routable
{
    public function __invoke(Auth $auth, NeoanApp $app): array
    {
        $person = PersonModel::get(Request::getParameter('personId'))->withCustomer();
        // has user?
        $user = UserModel::retrieveOne(['personId' => $person->id]);
        $invitation = InvitationModel::retrieveOne([
            '^deletedAt',
            'email' => $person->email,
            'privilege' => Privilege::CUSTOMER->name,
            'companyId' => $auth->user->companyId
        ]);

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
        return [
            'person' => $person->toArray(),
            'hasUser' => (bool) $user,
            'invitation' => $invitation,
            'base' =>  base
        ];
    }
}