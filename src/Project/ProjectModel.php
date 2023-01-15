<?php

namespace App\Project;

use App\Customer\CustomerModel;
use App\Person\PersonModel;
use Neoan\Model\Attributes\IsForeignKey;
use Neoan\Model\Attributes\IsPrimaryKey;
use Neoan\Model\Attributes\Transform;
use Neoan\Model\Attributes\Type;
use Neoan\Model\Helper\DateTimeProperty;
use Neoan\Model\Model;
use Neoan\Model\Traits\TimeStamps;
use Neoan\Model\Transformers\LockedTimeIn;

class ProjectModel extends Model
{
    #[IsPrimaryKey]
    public int $id;

    #[IsForeignKey(CustomerModel::class)]
    public int $customerId;

    #[IsForeignKey(PersonModel::class)]
    public ?int $personId;

    public string $title;

    #[
        Type('datetime'),
        Transform(LockedTimeIn::class)
    ]
    public ?DateTimeProperty $targetedAt = null;

    use TimeStamps;

}