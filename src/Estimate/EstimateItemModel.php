<?php

namespace App\Estimate;

use App\Milestone\MilestoneModel;
use App\Product\ProductModel;
use Neoan\Model\Attributes\IsForeignKey;
use Neoan\Model\Attributes\IsPrimaryKey;
use Neoan\Model\Model;

/**
 * @method ProductModel product
 * @method MilestoneModel milestone
 *
 */
class EstimateItemModel extends Model
{
    #[IsPrimaryKey]
    public int $id;

    #[IsForeignKey(EstimateModel::class)]
    public int $estimateId;

    #[IsForeignKey(MilestoneModel::class)]
    public int $milestoneId;

    #[IsForeignKey(ProductModel::class)]
    public int $productId;

    public int $estimatedHours;

    protected function afterStore(): void
    {
        $this->calculateEstimate();
    }
    protected function afterDeletion(): void
    {
        $this->calculateEstimate();
    }
    private function calculateEstimate(): void
    {
        $estimatedOnMilestone = 0;
        EstimateItemModel::retrieve(['milestoneId' => $this->milestoneId])->each(function (EstimateItemModel $item) use (&$estimatedOnMilestone){
            $estimatedOnMilestone += $item->estimatedHours;
        });
        $milestone = MilestoneModel::get($this->milestoneId);
        $milestone->estimatedHours = $estimatedOnMilestone;
        $milestone->store();
    }

}