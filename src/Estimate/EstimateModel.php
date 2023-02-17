<?php

namespace App\Estimate;

use App\Product\BillingType;
use App\Project\ProjectModel;
use Neoan\Model\Attributes\Computed;
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

/**
 * @method ProjectModel project;
 * */
class EstimateModel extends Model
{
    #[IsPrimaryKey]
    public int $id;

    #[IsForeignKey(ProjectModel::class)]
    public int $projectId;

    #[HasMany(EstimateItemModel::class, ['estimateId' => 'id'])]
    public ?Collection $items;

    #[
        Type('datetime'),
        Transform(LockedTimeIn::class)
    ]
    public ?DateTimeProperty $lockedInAt;

    #[
        Type('datetime'),
        Transform(LockedTimeIn::class)
    ]
    public ?DateTimeProperty $sentAt;

    #[Computed]
    public function isSent(): bool
    {
        return !isset($this->sentAt) || empty($this->sentAt->value);
    }

    #[Computed]
    public function isLocked(): bool
    {
        return isset($this->lockedInAt) && isset($this->lockedInAt->value);
    }

    #[Computed]
    public function totals(): array
    {
        $all = [
            'hours' => 0,
            'net' => 0
        ];
        $milestones = [];
        $this->items->each(function (EstimateItemModel $item) use (&$all, &$milestones) {
            if (!isset($milestones[$item->milestone()->title])) {
                $milestones[$item->milestone()->title] = [
                    'hours' => 0,
                    'net' => 0
                ];
            }
            $all['hours'] += $item->estimatedHours;
            $milestones[$item->milestone()->title]['hours'] += $item->estimatedHours;
            $product = $item->product();
            $cost = $product->billingType === BillingType::HOURLY ? $product->price * $item->estimatedHours : $product->price;
            $milestones[$item->milestone()->title]['net'] += $cost;
            $all['net'] += $cost;

        });
        return [
            'total' => $all,
            'milestones' => [... $milestones]
        ];
    }


    public function itemsByMilestones(): array
    {
        $knownProducts = [];
        $milestones = [];
        $result = [];
        $this->items->each(function (EstimateItemModel $item) use (&$milestones, &$knownProducts, &$result) {

            if (!array_key_exists($item->productId, $knownProducts)) {
                $knownProducts[$item->productId] = $item->product();
            }
            if (!array_key_exists($item->milestoneId, $milestones)) {
                $milestones[$item->milestoneId] = $item->milestone();
            }
            $product = $knownProducts[$item->productId];
            $result[] = [
                ...$item->toArray(),
                'milestone' => $milestones[$item->milestoneId]->toArray(),
                'product' => $product,
                'net' => $product->billingType === BillingType::FLATRATE ? $product->price : $product->price * $item->estimatedHours
            ];

        });
        usort($result, function ($a, $b) {
            if ($a['milestoneId'] === $b['milestoneId']) {
                return 0;
            }
            return $a['milestoneId'] < $b['milestoneId'] ? -1 : 1;
        });
        return $result;

    }


    use TimeStamps;
}