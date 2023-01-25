<?php

namespace App\Note;

use Neoan\Model\Attributes\Computed;
use Neoan\Model\Attributes\IsEnum;
use Neoan\Model\Attributes\IsPrimaryKey;
use Neoan\Model\Attributes\Transform;
use Neoan\Model\Attributes\Type;
use Neoan\Model\Helper\DateTimeProperty;
use Neoan\Model\Model;
use Neoan\Model\Traits\TimeStamps;
use Neoan\Model\Transformers\LockedTimeIn;

class NoteModel extends Model
{
    #[IsPrimaryKey]
    public int $id;

    #[Type('MEDIUMTEXT')]
    public string $content;

    public int $relationId;

    #[IsEnum(NoteType::class)]
    public NoteType $noteType;

    #[Type('datetime'), Transform(LockedTimeIn::class)]
    public ?DateTimeProperty $remindAt = null;

    use TimeStamps;

    public function calendarEvent(): ?array
    {
        if(isset($this->remindAt->dateTime)){
            return [
                'id' => 'note-' . $this->id,
                'start' => $this->remindAt->dateTime->format("Y-m-d\\TH:i:sO"),
                'allDay' => false,
                'title' => substr($this->content, 0,12) . '...',
                'url' =>  '/' . $this->noteType->value . '/' . $this->relationId,
                'backgroundColor' => $this->noteType->color()
            ];
        }

        return null;
    }


}