<?php

namespace App\Settings;

use App\Company\CompanyModel;
use Neoan\Model\Attributes\IsForeignKey;
use Neoan\Model\Attributes\IsPrimaryKey;
use Neoan\Model\Attributes\Type;
use Neoan\Model\Model;
use Neoan\Model\Traits\TimeStamps;

class InvoiceSettingModel extends Model
{
    #[IsPrimaryKey]
    public int $id;

    #[IsForeignKey(CompanyModel::class)]
    public int $companyId;

    public int $invoiceNumber;

    public int $invoiceNumberPadding;

    public string $invoiceNumberFormat;

    #[Type('MEDIUMTEXT')]
    public ?string $footer;

    use TimeStamps;

}