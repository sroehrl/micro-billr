<?php

namespace App\Auth;

use App\Invitation\InvitationModel;
use App\User\UserModel;
use Config\FormPost;
use Neoan\Enums\RequestMethod;
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
                $feedback = 'wrong code';
            } else {
                $user = new UserModel();
                $user->email = Request::getInput('email');
                $user->password = Request::getInput('password');
                $user->privilege = $invitation->privilege;
                $user->store();
                $invitation->deletedAt->set('now');
                $invitation->store();
                Response::redirect('/');
            }
        }
        return ['error' => $feedback];
    }
}