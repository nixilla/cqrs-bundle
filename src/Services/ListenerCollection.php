<?php

namespace Nixilla\CqrsBundle\Services;

class ListenerCollection
{
    private $listeners = [];

    public function addListener($listener, $eventName)
    {
        $this->listeners[$eventName][] = $listener;
    }

    public function getListeners($eventName)
    {
        return isset($this->listeners[$eventName])
            ? $this->listeners[$eventName]
            : []
        ;
    }
}
