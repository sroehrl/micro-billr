<?php

namespace App\Note;

use App\Auth\BehindLogin;
use Neoan\Request\Request;
use Neoan\Routing\Attributes\Delete;
use Neoan\Routing\Interfaces\Routable;

#[Delete('/api/note/:id', BehindLogin::class)]
class NoteDelete implements Routable
{
    public function __invoke(): array
    {
        NoteModel::get(Request::getParameter('id'))->delete();
        return ['request' => 'received'];
    }
}