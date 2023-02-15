<?php

namespace App\Milestone;

use App\Auth\BehindLogin;
use App\Milestone\Request\MilestoneRequest;
use Config\FormPost;
use Neoan\Response\Response;
use Neoan\Routing\Interfaces\Routable;

#[FormPost('/milestone/:projectId', null, BehindLogin::class)]
class MilestoneCreate implements Routable
{
    public function __invoke(MilestoneRequest $request): array
    {
        $milestone = new MilestoneModel([
            'projectId' => $request->projectId,
            'title' => $request->title,
            'targetedAt' => $request->targetedAt
        ]);

        $milestone->store();
        Response::redirect($_SERVER['HTTP_REFERER']);
    }
}