<?php

namespace App\Address;

use Neoan\Model\Attributes\IsPrimaryKey;
use Neoan\Model\Model;
use Neoan\Model\Traits\TimeStamps;

class AddressModel extends Model
{
    #[IsPrimaryKey]
    public int $id;

    public string $street;

    public ?string $state;

    public string $place;

    public string $postalCode;

    public string $country;

    use TimeStamps;

}