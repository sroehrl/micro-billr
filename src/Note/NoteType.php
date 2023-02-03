<?php

namespace App\Note;

use App\Project\ProjectStatus;

enum NoteType: string
{
    case PROJECT = 'project';
    case PERSON = 'person';
    case TIMESHEET = 'timesheet';
    case MILESTONE = 'milestone';

    public function color(?ProjectStatus $status = null): string
    {
        return match ($this){
            self::MILESTONE => isset($status) && $status->value === 'in progress' ? '#b464c5c7' : '#b464c550',
            self::PROJECT => '#27b3e1',
            self::TIMESHEET => '#298145a1',
            self::PERSON => 'yellow'
        };
    }

}
