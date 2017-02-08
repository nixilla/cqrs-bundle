<?php

namespace Nixilla\CqrsBundle\Services;

class ProjectorCollection
{
    private $projectors = [];

    public function addProjector($projector, $eventName)
    {
        $this->projectors[$eventName][] = $projector;
    }

    public function getProjectors($eventName)
    {
        return isset($this->projectors[$eventName])
            ? $this->projectors[$eventName]
            : []
        ;
    }
}
