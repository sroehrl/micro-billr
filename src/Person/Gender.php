<?php

namespace App\Person;

enum Gender: int
{
    case MALE = 1;
    case FEMALE = 0;
    case UNSPECIFIED = 2;
}
