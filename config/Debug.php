<?php

namespace Config;

use Neoan\Enums\GenericEvent;
use Neoan\Event\Event;
use Neoan\Helper\Terminate;

class Debug
{
    public static function beforeTransaction(): void
    {
        Event::on(GenericEvent::BEFORE_DATABASE_TRANSACTION, function($ev){
            var_dump($ev);
            Terminate::die();
        });
    }
    public static function afterTransaction(): void
    {
        Event::on(GenericEvent::AFTER_DATABASE_TRANSACTION, function ($ev){
            var_dump($ev);
            Terminate::die();
        });
    }
}