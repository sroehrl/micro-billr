<?php

namespace App\Timeline;

use App\Product\ProductModel;
use Neoan\Model\Attributes\IsEnum;
use Neoan\Model\Attributes\IsForeignKey;
use Neoan\Model\Attributes\IsPrimaryKey;
use Neoan\Model\Attributes\Type;
use Neoan\Model\Model;
use Neoan\Model\Traits\TimeStamps;

class TimelineModel extends Model
{
    #[IsPrimaryKey]
    public int $id;

    #[IsForeignKey(ProductModel::class)]
    public int $projectId;

    #[IsEnum(TimelineActivity::class)]
    public ?TimelineActivity $activity;

    #[Type('text')]
    public ?string $content;

    use TimeStamps;

    public function __invoke(): static
    {
        return $this;
    }

}