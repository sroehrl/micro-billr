<?php

namespace App\Person;

use App\Customer\CustomerModel;
use Neoan\Model\Attributes\IsEnum;
use Neoan\Model\Attributes\IsForeignKey;
use Neoan\Model\Attributes\IsPrimaryKey;
use Neoan\Model\Model;
use Neoan\Model\Traits\TimeStamps;

/**
 * @method CustomerModel customer
 * @method PersonModel withCustomer
 */
class PersonModel extends Model
{
    #[IsPrimaryKey]
    public int $id;

    #[IsForeignKey(CustomerModel::class)]
    public int $customerId;

    public string $title = 'Employee';

    #[IsEnum(Gender::class)]
    public Gender $gender;

    public string $firstName;

    public ?string $middleName;

    public string $lastName;

    public ?string $email;

    public ?string $phone;

    use TimeStamps;

}