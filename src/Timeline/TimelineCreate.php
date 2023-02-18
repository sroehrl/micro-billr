<?php

namespace App\Timeline;

use App\Helper\FeedbackWrapper;
use Neoan\Routing\Attributes\Post;
use Neoan\Routing\Interfaces\Routable;

#[Post('/timeline/:projectId')]
class TimelineCreate implements Routable
{
    public function __invoke(TimelineCreateRequest $request, TimelineModel $timeline): void
    {
        $timeline->projectId = $request->projectId;
        $timeline->activity = $request->activity;
        $timeline->content = $request->content;
        $timeline->store();
        FeedbackWrapper::redirectBack('Timeline item added');
    }
}