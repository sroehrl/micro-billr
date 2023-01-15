<?php

namespace App\Project;

use App\Customer\CustomerModel;
use App\Person\PersonModel;
use Neoan\Model\Attributes\IsForeignKey;
use Neoan\Model\Attributes\IsPrimaryKey;
use Neoan\Model\Model;
use Neoan\Model\Traits\TimeStamps;

class ProjectModel extends Model
{
    #[IsPrimaryKey]
    public int $id;

    #[IsForeignKey(CustomerModel::class)]
    public int $customerId;

    #[IsForeignKey(PersonModel::class)]
    public int $personId;

    public string $title;

    use TimeStamps;

}