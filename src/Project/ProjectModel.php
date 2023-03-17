<?php

namespace App\Project;

use App\Company\CompanyModel;
use App\Customer\CustomerModel;
use App\Person\PersonModel;
use App\User\UserModel;
use Config\ShortSlugTransformer;
use Config\TargetDate;
use Neoan\Model\Attributes\IsEnum;
use Neoan\Model\Attributes\IsForeignKey;
use Neoan\Model\Attributes\IsPrimaryKey;
use Neoan\Model\Attributes\Transform;
use Neoan\Model\Collection;
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

    #[IsForeignKey(CompanyModel::class)]
    public int $companyId;

    #[IsForeignKey(PersonModel::class)]
    public ?int $personId;

    public string $title;

    #[Transform(ShortSlugTransformer::class)]
    public string $slug;

    #[IsEnum(ProjectStatus::class)]
    public ProjectStatus $status = ProjectStatus::PLANNED;

    public function calendarEvent(): array
    {
        $event = [
            'id' => 'project-' . $this->id,
            'allDay' => true,
            'title' => $this->customer()->title . ' - ' . $this->title,
            'url' => '/project/' . $this->id,
            'backgroundColor' => $this->status->color(),
            'extendedProps' => $this->toArray()
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