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


        Store::write('pageTitle', 'Login');
        if(Request::getInput('email')){
            if(Request::getInput('remember')){
                setcookie('email', Request::getInput('email'));
            }
            [
                'email' => $email,
                'password' => $password,
            ] = Request::getInputs();
            if(UserModel::login($email, $password)){
                $afterLogin = $_COOKIE['afterLogin'] ?? '/';
                unset($_COOKIE['afterLogin']);
                Response::redirect($afterLogin);
            }
            Response::redirect('/login?error=Wrong credentials!');
        }


        return [
            'name' => 'Login',
            'email' => $_COOKIE['email'] ?? '',
            'error' => Request::getQuery('error') ?? ''
        ];
    }
}