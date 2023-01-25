<?php

namespace App\Note;

enum NoteType: string
{
    case PROJECT = 'project';
    case PERSON = 'person';
    case TIMESHEET = 'timesheet';
    case MILESTONE = 'milestone';

    public function color(): string
    {
        return match ($this){
            self::MILESTONE => 'blue',
            self::PROJECT => 'yellow',
            self::TIMESHEET => 'green',
            self::PERSON => 'purple'
        };
    }
}
