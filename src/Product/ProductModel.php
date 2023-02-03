<?php

namespace App\Product;

use Neoan\Model\Attributes\Computed;
use Neoan\Model\Attributes\IsEnum;
use Neoan\Model\Attributes\IsPrimaryKey;
use Neoan\Model\Attributes\Transform;
use Neoan\Model\Attributes\Type;
use Neoan\Model\Model;
use Neoan\Model\Traits\TimeStamps;

class ProductModel extends Model
{
    #[IsPrimaryKey]
    public int $id;

    public string $name;

    public ?string $productNumber;

    #[Type('MEDIUMTEXT')]
    public string $description;

    #[Computed]
    public function htmlDescription(): ?string
    {
        return isset($this->description) ? '<p>' . nl2br($this->description) . '</p>' : '';
    }

    #[IsEnum(BillingType::class)]
    public ?BillingType $billingType;

    #[
        Transform(PriceTransformation::class),
        Type('int', 11)
    ]
    public float $price;

    public ?bool $taxable = false;

    use TimeStamps;
}