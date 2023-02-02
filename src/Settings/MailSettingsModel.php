<?php

namespace App\Settings;

use App\Company\CompanyModel;
use Config\EncryptionTransformer;
use Neoan\Model\Attributes\IsEnum;
use Neoan\Model\Attributes\IsForeignKey;
use Neoan\Model\Attributes\IsPrimaryKey;
use Neoan\Model\Attributes\Transform;
use Neoan\Model\Model;
use Neoan\Model\Traits\TimeStamps;

class MailSettingsModel extends Model
{
    #[IsPrimaryKey]
    public int $id;

    #[IsForeignKey(CompanyModel::class)]
    public int $companyId;

    public string $senderName;

    public string $senderEmail;

    public string $publicKey;

    #[Transform(EncryptionTransformer::class)]
    public string $privateKey;

    #[IsEnum(MailProvider::class)]
    public ?MailProvider $mailProvider;

    use TimeStamps;

}