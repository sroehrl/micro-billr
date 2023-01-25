<?php

namespace App\Note;

use App\Auth\BehindLogin;
use App\Helper\FeedbackWrapper;
use Neoan\Request\Request;
use Neoan\Response\Response;
use Neoan\Routing\Attributes\Post;
use Neoan\Routing\Interfaces\Routable;

#[Post('/note/:noteType/:relationId', BehindLogin::class)]
class NoteCreate implements Routable
{
    public function __invoke(): array
    {
        $noteModel = new NoteModel([
            'content' => Request::getInput('content'),
            'noteType' => NoteType::from(Request::getParameter('noteType')),
            'relationId' => Request::getParameter('relationId')
        ]);
        if(Request::getInput('remindAt')){
            $noteModel->remindAt->set(Request::getInput('remindAt'));
        }
        $noteModel->store();
        FeedbackWrapper::redirectBack('Note saved!');

    }
}