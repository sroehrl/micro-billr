<?php

namespace App\Calendar;

use App\Milestone\MilestoneModel;
use App\Note\NoteModel;
use App\Note\NoteType;
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
        self::notes();

        // Milestones
        self::milestones();
        return self::$allEvents;
    }

    public static function forProject(int $projectId): array
    {
        self::$allEvents[] = ProjectModel::get($projectId)->calendarEvent();
        self::notes(['relationId' => $projectId, 'noteType' => NoteType::PROJECT->value]);
        self::milestones(['projectId' => $projectId]);
        return self::$allEvents;
    }
    private static function notes(?array $conditions = []): void
    {
        NoteModel::retrieve([...$conditions, '^deletedAt'])->each(function(NoteModel $note){
            if($event = $note->calendarEvent()){
                self::$allEvents[] = $event;
            }
        });
    }

    private static function milestones(?array $conditions = []): void
    {
        MilestoneModel::retrieve([...$conditions, '^deletedAt'])->each(function(MilestoneModel $milestone){
            if($event = $milestone->calendarEvent()){
                self::$allEvents[] = $event;
            }
        });
    }
}