<?php

namespace App\Milestone;

use App\Auth\BehindLogin;
use App\Helper\FeedbackWrapper;
use Neoan\Enums\GenericEvent;
use Neoan\Event\Event;
use Neoan\Request\Request;
use Neoan\Response\Response;
use Neoan\Routing\Attributes\Post;
use Neoan\Routing\Interfaces\Routable;

#[Post('/milestone/:id/update', BehindLogin::class)]
class MilestoneUpdate implements Routable
{
    public function __invoke(): array
    {
        /*Event::on(GenericEvent::BEFORE_DATABASE_TRANSACTION, function($ev){
            var_dump($ev);
        });*/
        $milestone = MilestoneModel::get(Request::getParameter('id'));
        $milestone->targetedAt->set(Request::getInput('targetedAt'));
        if(Request::getInput('startedAt')){
            $milestone->startedAt->set(Request::getInput('startedAt'));
        }

        $milestone->estimatedHours = Request::getInput('estimatedHours');
        $milestone->store();
        FeedbackWrapper::redirectBack('Milestone updated');

    }
}