<?php

namespace App\Project;

use App\Customer\CustomerModel;
use App\Note\NoteType;
use App\Person\PersonModel;
use Config\TargetDate;
use Neoan\Model\Attributes\IsEnum;
use Neoan\Model\Attributes\IsForeignKey;
use Neoan\Model\Attributes\IsPrimaryKey;
use Neoan\Model\Model;
use Neoan\Model\Traits\TimeStamps;

/**
 * @method ProjectModel withCustomer()
 * @method ProjectModel withPerson()
 * @method PersonModel person
 * @method CustomerModel customer
 */
class ProjectModel extends Model
{
    #[IsPrimaryKey]
    public int $id;

    #[IsForeignKey(CustomerModel::class)]
    public int $customerId;

    #[IsForeignKey(PersonModel::class)]
    public ?int $personId;

    public string $title;

    #[IsEnum(ProjectStatus::class)]
    public ProjectStatus $status = ProjectStatus::PLANNED;

    public function calendarEvent(): array
    {
        $event = [
            'id' => 'project-' . $this->id,
            'allDay' => true,
            'title' => $this->customer()->title . ' - ' . $this->title,
            'url' => '/project/' . $this->id,
            'backgroundColor' => $this->status->color()
        ];
        if(isset($this->startedAt->dateTime)){
            $event['start'] = $this->startedAt->dateTime->format("Y-m-d\\TH:i:sO");
        }
        if(isset($this->targetedAt->dateTime)){
            $event['end'] = $this->targetedAt->dateTime->format("Y-m-d\\TH:i:sO");
        }
        return $event;
    }

   use TimeStamps;
   use TargetDate;

   protected function afterStore(): void
   {
       if(!$this->startedAt->value && $this->status === ProjectStatus::IN_PROGRESS) {

           $this->startedAt->set('now');
           $this->store();
       }
   }
}