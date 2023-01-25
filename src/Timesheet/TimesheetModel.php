<?php

namespace App\Timesheet;

use App\Bill\BillModel;
use App\Milestone\MilestoneModel;
use App\Product\PriceTransformation;
use App\Product\ProductModel;
use App\Project\ProjectModel;
use Neoan\Database\Database;
use Neoan\Model\Attributes\Computed;
use Neoan\Model\Attributes\IsForeignKey;
use Neoan\Model\Attributes\IsPrimaryKey;
use Neoan\Model\Attributes\Transform;
use Neoan\Model\Attributes\Type;
use Neoan\Model\Helper\DateTimeProperty;
use Neoan\Model\Model;
use Neoan\Model\Traits\TimeStamps;
use Neoan\Model\Transformers\LockedTimeIn;

/**
 * @method TimesheetModel withMilestone
 * @method TimesheetModel withProduct
 * @method TimesheetModel withProject
 */
class TimesheetModel extends Model
{
    #[IsPrimaryKey]
    public int $id;

    #[IsForeignKey(MilestoneModel::class)]
    public int $milestoneId;

    #[IsForeignKey(ProductModel::class)]
    public int $productId;

    #[IsForeignKey(ProjectModel::class)]
    public int $projectId;

    #[
        Transform(PriceTransformation::class),
        Type('int', 11)
    ]
    public float $hours;

    #[IsForeignKey(BillModel::class)]
    public ?int $billId;

    #[
        Type('date'),
        Transform(LockedTimeIn::class)
    ]
    public ?DateTimeProperty $workedAt = null;

    use TimeStamps;

    #[Computed]
    public function productName()
    {
        return isset($this->productId) ? Database::easy('product_model.name', ['id' => $this->productId])[0]['name'] : '';
    }

    protected function afterStore(): void
    {
        // calculations
        $actualHours = 0;
        $timeSheets = TimesheetModel::retrieve(['^deletedAt','milestoneId' => $this->milestoneId]);
        foreach ($timeSheets as $timeSheet){
            $actualHours += $timeSheet->hours;
        }
        $milestone = MilestoneModel::get($this->milestoneId);
        $milestone->actualHours = $actualHours;
        // milestone started?
        if(!$milestone->startedAt->value){
            $milestone->startedAt->set('now');
        }
        $milestone->store();

    }

}