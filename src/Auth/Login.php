<?php

namespace App\Auth;

use App\User\Privilege;
use App\User\UserModel;
use Neoan\Request\Request;
use Neoan\Response\Response;
use Neoan\Routing\Attributes\Post;
use Neoan\Routing\Attributes\Web;
use Neoan\Routing\Interfaces\Routable;
use Neoan\Store\Store;

#[Web('/login', 'Auth/views/login.html')]
#[Post('/login')]
class Login implements Routable
{
    public function __invoke(): array
    {
        /*$newUser = new UserModel([
            'email' => 'stefan.roehrl@outlook.com',
            'password' => '123123',
        ]);
        $newUser->privilege = Privilege::ADMIN;
        $newUser->store();*/


        Store::write('pageTitle', 'Login');
        if(Request::getInput('email')){
            [
                'email' => $email,
                'password' => $password,
            ] = Request::getInputs();
            if(UserModel::login($email, $password)){
                Response::redirect('/');
            }
            Response::redirect('/login?error=Wrong credentials!');
        }


        return ['name' => 'Login', 'error' => Request::getQuery('error') ?? ''];
    }
}