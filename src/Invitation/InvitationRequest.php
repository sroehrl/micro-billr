<?php

namespace App\Invitation;

use App\User\Privilege;
use Neoan\Request\RequestGuard;

class InvitationRequest extends RequestGuard
{
    public Privilege $privilege;

    public string $email;

}