<?php

namespace App\Bill;

use App\Company\CompanyModel;
use App\Customer\CustomerModel;
use App\Product\BillingType;
use App\Product\PriceTransformation;
use App\Project\ProjectModel;
use App\Timesheet\TimesheetModel;
use Neoan\Database\Database;
use Neoan\Model\Attributes\Computed;
use Neoan\Model\Attributes\HasMany;
use Neoan\Model\Attributes\IsEnum;
use Neoan\Model\Attributes\IsForeignKey;
use Neoan\Model\Attributes\IsPrimaryKey;
use Neoan\Model\Attributes\Transform;
use Neoan\Model\Attributes\Type;
use Neoan\Model\Collection;
use Neoan\Model\Helper\DateTimeProperty;
use Neoan\Model\Model;
use Neoan\Model\Traits\TimeStamps;
use Neoan\Model\Transformers\CurrentTimeIn;
use Neoan\Model\Transformers\LockedTimeIn;

/**
 * @method BillModel withCustomer()
 * @method CustomerModel customer()
 * @method BillModel withProject()
 * @method ProjectModel project()
 * @property Collection lineItems
 */
class BillModel extends Model
{
    #[IsPrimaryKey]
    public int $id;

    #[IsForeignKey(ProjectModel::class)]
    public int $projectId;

    #[IsForeignKey(CustomerModel::class)]
    public int $customerId;

    #[IsForeignKey(CompanyModel::class)]
    public int $companyId;

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

    #[IsEnum(BillStatus::class)]
    public ?BillStatus $billStatus = BillStatus::PROCESSING;

    #[Computed]
    public function lineItems(): Collection
    {
        if(!isset($this->id)){
            return new Collection();
        }
        $knownFlatrates = [];
        return TimesheetModel::retrieve(['^deletedAt','billId'=>$this->id],['orderBy' =>['milestoneId','ASC']])
            ->each(fn(TimesheetModel $model) => $model->withProduct()->withMilestone())
            ->each(function(TimesheetModel $lineItem) use (&$knownFlatrates){
                if($lineItem->product->billingType === BillingType::FLATRATE){
                    $lineItem->total = in_array($lineItem->productId, $knownFlatrates) ? 0 : $lineItem->product->price;
                    $knownFlatrates[] = $lineItem->productId;
                } else {
                    $lineItem->total = $lineItem->product->price * $lineItem->hours;
                }
            });

    }
    #[
        Type('datetime'),
        Transform(CurrentTimeIn::class)
    ]
    public ?DateTimeProperty $sentDate;

    #[
        Type('datetime'),
        Transform(CurrentTimeIn::class)
    ]
    public ?DateTimeProperty $generatedDate;

    use TimeStamps;

    protected function afterDeletion(): void
    {
        parent::afterDeletion();
        Database::raw('UPDATE timesheet_model SET billId = NULL WHERE billId = {{id}}', [
            'id' => $this->id
        ]);
    }

}