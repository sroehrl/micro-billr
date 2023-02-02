<?php

namespace App\Address;

use Neoan\Model\Attributes\Computed;
use Neoan\Model\Attributes\IsEnum;
use Neoan\Model\Attributes\IsPrimaryKey;
use Neoan\Model\Model;
use Neoan\Model\Traits\TimeStamps;
use Neoan3\Apps\Template\Template;

class AddressModel extends Model
{
    #[IsPrimaryKey]
    public int $id;

    public string $street;

    public ?string $state;

    public string $place;

    public string $postalCode;

    #[IsEnum(Country::class)]
    public Country $country;

    #[Computed]
    public function printableAddress(): string
    {
        if(isset($this->country)){
            return Template::embrace($this->country->addressFormat(), $this->toArray());
        }
        return 'Country missing';
    }

    use TimeStamps;
}