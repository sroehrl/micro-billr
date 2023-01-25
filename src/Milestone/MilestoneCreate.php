<?php

namespace App\Milestone;

use App\Auth\BehindLogin;
use Neoan\Enums\GenericEvent;
use Neoan\Event\Event;
use Neoan\Request\Request;
use Neoan\Response\Response;
use Neoan\Routing\Attributes\Post;
use Neoan\Routing\Interfaces\Routable;

#[Post('/milestone/:projectId', BehindLogin::class)]
class MilestoneCreate implements Routable
{
    public function __invoke(): array
    {
        $milestone = new MilestoneModel([
            'projectId' => Request::getParameter('projectId')
        ]);
        [
            'title' => $milestone->title,
        ] = Request::getInputs();
        $milestone->targetedAt->set(Request::getInput('targetedAt'));
        $milestone->store();
        Response::redirect($_SERVER['HTTP_REFERER']);
    }
}