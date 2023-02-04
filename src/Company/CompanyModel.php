<?php

namespace App\Company;

use App\Address\AddressModel;
use Neoan\Model\Attributes\IsPrimaryKey;
use Neoan\Model\Traits\TimeStamps;

class CompanyModel extends AddressModel
{
    #[IsPrimaryKey]
    public int $id;

    public string $name;



    public string $place;

    public string $postalCode;


    public string $bankName;

    public string $accountName;

    public string $routingNumber;

    public string $accountNumber;

    public string $swiftBic;

    public string $registry;

    public ?string $phoneNumber;

    public ?string $color;

    public ?string $website;

    use TimeStamps;

}