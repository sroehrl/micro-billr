<?php

namespace App\Project;

enum ProjectStatus: string
{
    case PROPOSED = 'proposed';
    case PLANNED = 'planned';
    case IN_PROGRESS = 'in progress';
    case ON_HOLD = 'on hold';
    case ABORTED = 'aborted';
    case COMPLETED = 'completed';

    public function color(): string
    {
        return match ($this){
            self::PROPOSED,
            self::PLANNED => '#2792b563',
            self::IN_PROGRESS => '#27b3e1',
            self::ON_HOLD => 'purple',
            self::COMPLETED => '#48c95be3',
            default => 'red'
        };
    }
}
