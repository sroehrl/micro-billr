<?php

namespace App\Customer;

use App\Address\AddressModel;
use App\Person\PersonModel;
use Neoan\Model\Attributes\HasMany;
use Neoan\Model\Attributes\IsForeignKey;
use Neoan\Model\Attributes\IsPrimaryKey;
use Neoan\Model\Collection;
use Neoan\Model\Model;
use Neoan\Model\Traits\TimeStamps;

/**
 * @method CustomerModel withAddress
 */
class CustomerModel extends Model
{
    #[IsPrimaryKey]
    public int $id;

    public string $title;

    public ?string $customerNumber;

    public ?string $registry;

    #[IsForeignKey(AddressModel::class)]
    public ?int $addressId;

    public string $emailAddress;

    #[HasMany(PersonModel::class, ['customerId' => 'id'])]
    public Collection $persons;


    use TimeStamps;
}