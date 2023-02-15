<?php

namespace Config;

use Neoan\Request\Request;

trait Autowireable
{
    public function __invoke(): static
    {
        if(Request::getParameter('id')){
            return static::get(Request::getParameter('id'));
        }
        return new static();
    }
}