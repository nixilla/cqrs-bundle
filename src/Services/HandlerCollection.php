<?php

namespace Nixilla\CqrsBundle\Services;

class HandlerCollection
{
    private $handlers = [];

    public function setHandler($callable, $eventName)
    {
        $this->handlers[$eventName] = $callable;
    }

    public function getHandler($eventName)
    {
        return isset($this->handlers[$eventName]) ? $this->handlers[$eventName] : null;
    }
}
