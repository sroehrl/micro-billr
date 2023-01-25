<?php

namespace App\Milestone;

use App\Note\NoteModel;
use App\Note\NoteType;
use App\Project\ProjectModel;
use App\Timesheet\TimesheetModel;
use Config\TargetDate;
use Neoan\Enums\GenericEvent;
use Neoan\Enums\TimePeriod;
use Neoan\Enums\TransactionType;
use Neoan\Event\Event;
use Neoan\Model\Attributes\Computed;
use Neoan\Model\Attributes\HasMany;
use Neoan\Model\Attributes\IsForeignKey;
use Neoan\Model\Attributes\IsPrimaryKey;
use Neoan\Model\Collection;
use Neoan\Model\Model;
use Neoan\Model\Traits\Setter;
use Neoan\Model\Traits\TimeStamps;

/**
 * @method MilestoneModel withProject()
 */
class MilestoneModel extends Model
{
    #[IsPrimaryKey]
    public int $id;

    public string $title;

    #[IsForeignKey(ProjectModel::class)]
    public int $projectId;

    #[HasMany(TimesheetModel::class, ['id' => 'milestoneId'])]
    public Collection $timeSheets;


    public ?float $estimatedHours = 0.0;

    public ?float $actualHours = 0.0;

    #[Computed]
    public function notes(): Collection
    {

        if(isset($this->id)){
            return NoteModel::retrieve(['relationId' => $this->id, 'noteType' => 'milestone', '^deletedAt']);
        }
        return new Collection();
    }

    use TargetDate;
    use TimeStamps;
    use Setter;

    public function calendarEvent(): ?array
    {
        $event = [
            'id' => 'milestone-' . $this->id,
            'end' => $this->targetedAt->dateTime->format("Y-m-d\\TH:i:sO"),
            'allDay' => true,
            'title' => $this->title,
            'url' =>  '/milestone/' . $this->id,
            'backgroundColor' => NoteType::MILESTONE->color()
        ];
        if(isset($this->startedAt->dateTime)){
            $event['start'] = $this->startedAt->dateTime->format("Y-m-d\\TH:i:sO");
        } else {
            $event['start'] = $this->targetedAt->subtractTimePeriod(1, TimePeriod::DAYS)->dateTime->format("Y-m-d\\TH:i:sO");
        }
        return $event;
    }


}