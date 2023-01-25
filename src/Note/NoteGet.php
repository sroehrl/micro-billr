<?php

namespace App\Note;

use App\Auth\BehindLogin;
use Neoan\Model\Collection;
use Neoan\Request\Request;
use Neoan\Routing\Attributes\Get;
use Neoan\Routing\Interfaces\Routable;

#[Get('/api/note/:noteType/:relationId', BehindLogin::class)]
class NoteGet implements Routable
{
    public function __invoke(): Collection
    {
        return NoteModel::retrieve([
            '^deletedAt',
            'noteType' => Request::getParameter('noteType'),
            'relationId' => Request::getParameter('relationId')
            ]);
    }
}