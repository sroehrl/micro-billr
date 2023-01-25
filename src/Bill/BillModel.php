<?php

namespace App\Bill;

use App\Customer\CustomerModel;
use App\Product\PriceTransformation;
use App\Project\ProjectModel;
use App\Timesheet\TimesheetModel;
use Neoan\Model\Attributes\HasMany;
use Neoan\Model\Attributes\IsForeignKey;
use Neoan\Model\Attributes\IsPrimaryKey;
use Neoan\Model\Attributes\Transform;
use Neoan\Model\Attributes\Type;
use Neoan\Model\Collection;
use Neoan\Model\Helper\DateTimeProperty;
use Neoan\Model\Model;
use Neoan\Model\Traits\TimeStamps;
use Neoan\Model\Transformers\LockedTimeIn;

class BillModel extends Model
{
    #[IsPrimaryKey]
    public int $id;

    #[IsForeignKey(ProjectModel::class)]
    public int $projectId;

    #[IsForeignKey(CustomerModel::class)]
    public int $customerId;

    public string $billNumber;
    #[
        Transform(PriceTransformation::class),
        Type('int', 11)
    ]
    public float $totalNet;

    #[
        Type('datetime'),
        Transform(LockedTimeIn::class)
    ]
    public ?DateTimeProperty $paidAt;

    public ?string $transactionCode;

    #[HasMany(TimesheetModel::class, ['billId' => 'id', '^deletedAt'])]
    public Collection $lineItems;

    use TimeStamps;

}