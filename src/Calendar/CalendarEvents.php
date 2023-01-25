<?php

namespace App\Calendar;

use App\Milestone\MilestoneModel;
use App\Note\NoteModel;
use App\Project\ProjectModel;
use Neoan\Database\Database;

class CalendarEvents
{
    private static array $allEvents = [];
    public static function allEvents(): array
    {
        // projects
        ProjectModel::retrieve(['^deletedAt'])->each(function(ProjectModel $project){
            self::$allEvents[] = $project->calendarEvent();
        });
        // notes
        NoteModel::retrieve(['^deletedAt'])->each(function(NoteModel $note){
            if($event = $note->calendarEvent()){
                self::$allEvents[] = $event;
            }
        });
        // Milestones
        MilestoneModel::retrieve(['^deletedAt'])->each(function (MilestoneModel $milestone){
           self::$allEvents[] = $milestone->calendarEvent();
        });
        return self::$allEvents;
    }
}