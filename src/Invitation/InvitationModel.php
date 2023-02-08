<?php

namespace App\Invitation;

use App\User\Privilege;
use App\User\UserModel;
use Config\ShortSlugTransformer;
use Neoan\Model\Attributes\IsEnum;
use Neoan\Model\Attributes\IsForeignKey;
use Neoan\Model\Attributes\IsPrimaryKey;
use Neoan\Model\Attributes\Transform;
use Neoan\Model\Model;
use Neoan\Model\Traits\TimeStamps;

class InvitationModel extends Model
{
    #[IsPrimaryKey]
    public int $id;

    #[IsForeignKey(UserModel::class)]
    public int $inviterUserId;

    #[Transform(ShortSlugTransformer::class)]
    public string $inviteCode;

    public string $email;

    #[IsEnum(Privilege::class)]
    public ?Privilege $privilege;

    public ?int $companyId;

    use TimeStamps;

}