<?php

namespace App\User;

use Neoan\Database\Database;
use Neoan\Model\Attributes\IsEnum;
use Neoan\Model\Attributes\IsPrimaryKey;
use Neoan\Model\Attributes\IsUnique;
use Neoan\Model\Attributes\Transform;
use Neoan\Model\Model;
use Neoan\Model\Traits\TimeStamps;
use Neoan\Model\Transformers\Hash;
use Neoan3\Apps\Session;

class UserModel extends Model
{
    #[IsPrimaryKey]
    public int $id;

    #[IsUnique]
    public string $email;

    #[Transform(Hash::class)]
    public string $password;

    #[IsEnum(Privilege::class)]
    public ?Privilege $privilege;

    use TimeStamps;

    public static function login(string $email, string $password): ?UserModel
    {
        $user = Database::easy('user_model.id user_model.email user_model.password', ['email' => $email, '^deletedAt']);
        if(!empty($user) && password_verify($password, $user[0]['password'])){
            $user = UserModel::get($user[0]['id']);
            Session::login($user->id, [$user->privilege]);
            return $user;
        }
        return null;
    }

}