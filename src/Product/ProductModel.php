<?php

namespace App\Product;

use App\Company\CompanyModel;
use App\User\UserModel;
use Neoan\Model\Attributes\Computed;
use Neoan\Model\Attributes\IsEnum;
use Neoan\Model\Attributes\IsForeignKey;
use Neoan\Model\Attributes\IsPrimaryKey;
use Neoan\Model\Attributes\Transform;
use Neoan\Model\Attributes\Type;
use Neoan\Model\Collection;
use Neoan\Model\Model;
use Neoan\Model\Traits\TimeStamps;

class ProductModel extends Model
{
    #[IsPrimaryKey]
    public int $id;

    #[IsForeignKey(CompanyModel::class)]
    public int $companyId;

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

    public static function forCompany(UserModel $user): Collection
    {
        return ProductModel::retrieve(['^deletedAt', 'companyId' => $user->companyId]);
    }

    use TimeStamps;
}