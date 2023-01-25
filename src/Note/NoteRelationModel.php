<?php

namespace App\Note;

use Neoan\Model\Attributes\IsPrimaryKey;
use Neoan\Model\Model;

class NoteRelationModel extends Model
{
    #[IsPrimaryKey]
    public int $id;

}