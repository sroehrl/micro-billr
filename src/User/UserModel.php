<?php

namespace App\User;

use App\Company\CompanyModel;
use App\Person\PersonModel;
use Neoan\Database\Database;
use Neoan\Model\Attributes\IsEnum;
use Neoan\Model\Attributes\IsForeignKey;
use Neoan\Model\Attributes\IsPrimaryKey;
use Neoan\Model\Attributes\IsUnique;
use Neoan\Model\Attributes\Transform;
use Neoan\Model\Model;
use Neoan\Model\Traits\TimeStamps;
use Neoan\Model\Transformers\Hash;
use Neoan3\Apps\Session;

/**
 * @method CompanyModel company();
 * @method PersonModel|null person();
 */
class UserModel extends Model
{
    #[IsPrimaryKey]
    public int $id;

    #[IsUnique]
    public string $email;

    #[Transform(Hash::class)]
    public string $password;

    #[IsForeignKey(CompanyModel::class)]
    public ?int $companyId;

    #[IsForeignKey(PersonModel::class)]
    public ?int $personId;

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