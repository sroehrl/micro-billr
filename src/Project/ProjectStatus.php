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
}
