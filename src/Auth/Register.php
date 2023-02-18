<?php

namespace App\Auth;

use App\Invitation\InvitationModel;
use App\Person\PersonModel;
use App\User\Privilege;
use App\User\UserModel;
use Config\FormPost;
use Neoan\Enums\RequestMethod;
use Neoan\Errors\SystemError;
use Neoan\Request\Request;
use Neoan\Response\Response;
use Neoan\Routing\Attributes\Web;
use Neoan\Routing\Interfaces\Routable;

#[Web('/register', 'Auth/views/register.html')]
#[FormPost('/register', 'Auth/views/register.html')]
class Register implements Routable
{
    public function __invoke(): array
    {
        $feedback = '';
        if(Request::getRequestMethod() === RequestMethod::POST) {
            $invitation = InvitationModel::retrieveOne([
                'inviteCode' => Request::getInput('inviteCode'),
                'email' => Request::getInput('email'),
            ]);
            if(!$invitation){
                $feedback = 'wrong code or unregistered email';
            } else {

                $user = new UserModel();
                $user->email = Request::getInput('email');
                $user->password = Request::getInput('password');
                $user->privilege = $invitation->privilege;
                $user->companyId = $invitation->companyId;
                // customer?
                if($invitation->privilege === Privilege::CUSTOMER){
                    try{
                        $user->personId = PersonModel::retrieveOne([
                            'companyId' => $invitation->companyId,
                            'email' => $invitation->email,
                            '^deletedAt'
                        ])->id;
                    } catch (\Exception $e){
                        new SystemError('Error trying to grant privileges');
                    }

                }
                try{
                    $user->store();
                    $invitation->deletedAt->set('now');
                    $invitation->store();
                    Response::redirect('/');
                } catch (\Exception $e) {
                    new SystemError($e->getMessage());
                }

            }
        }
        return ['error' => $feedback];
    }
}