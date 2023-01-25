<?php

namespace Config;

use Neoan\Model\Attributes\Computed;
use Neoan\Model\Attributes\Transform;
use Neoan\Model\Attributes\Type;
use Neoan\Model\Helper\DateTimeProperty;
use Neoan\Model\Transformers\LockedTimeIn;

trait TargetDate
{
    #[
        Type('date'),
        Transform(LockedTimeIn::class)
    ]
    public ?DateTimeProperty $targetedAt = null;

    #[
        Type('date'),
        Transform(LockedTimeIn::class)
    ]
    public ?DateTimeProperty $startedAt = null;

    #[Computed]
    public function targetDate(): string
    {
        if(isset($this->targetedAt) && isset($this->targetedAt->dateTime)){
            return $this->targetedAt->dateTime->format('Y-m-d');
        }
        return '';
    }

    #[Computed]
    public function startDate(): string
    {
        if(isset($this->startedAt) && isset($this->startedAt->dateTime)){
            return $this->startedAt->dateTime->format('Y-m-d');
        }
        return '';
    }


}