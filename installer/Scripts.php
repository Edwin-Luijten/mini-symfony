<?php

namespace Installer;

use Composer\Script\Event;
use Installer\Handlers\CleanerHandler;
use Installer\Handlers\ParametersHandler;

class Scripts
{
    /**
     * @param Event $event
     */
    public static function postCreateProjectCmd(Event $event)
    {
        self::runHandler($event, new ParametersHandler());
        self::runHandler($event, new CleanerHandler());
    }

    /**
     * @param Event $event
     * @param Handlers\HandlerInterface $handler
     */
    private static function runHandler(Event $event, Handlers\HandlerInterface $handler)
    {
        $handler->setEvent($event)->execute();
    }
}