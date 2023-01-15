<?php

namespace App\Company;

use Neoan\Model\Attributes\IsPrimaryKey;
use Neoan\Model\Model;
use Neoan\Model\Traits\TimeStamps;

class CompanyModel extends Model
{
    #[IsPrimaryKey]
    public int $id;

    public string $name;

    public string $street;

    public string $state;

    public string $place;

    public string $postalCode;

    public string $country;

    public string $bankName;

    public string $accountName;

    public string $routingNumber;

    public string $accountNumber;

    public string $swiftBic;


    use TimeStamps;

}