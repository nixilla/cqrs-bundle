<?php

namespace Nixilla\CqrsBundle\Services;

use Prooph\Common\Event\ActionEvent;
use Prooph\Common\Event\ActionEventEmitter;
use Prooph\ServiceBus\EventBus;
use Prooph\ServiceBus\MessageBus;

class ProjectorResolver
{
    /** @var ProjectorCollection */
    private $collection;

    /**
     * ProjectorResolver constructor.
     * @param ProjectorCollection $collection
     */
    public function __construct(ProjectorCollection $collection)
    {
        $this->collection = $collection;
    }

    public function attach(ActionEventEmitter $dispatcher)
    {
        $dispatcher->attachListener(MessageBus::EVENT_ROUTE, [$this, 'onRoute']);
    }

    public function detach(ActionEventEmitter $dispatcher)
    {
        throw new \BadMethodCallException('Not implemented');
    }

    public function onRoute(ActionEvent $event)
    {
        $event->setParam(
            EventBus::EVENT_PARAM_EVENT_LISTENERS,
            array_merge(
                $event->getParam(EventBus::EVENT_PARAM_EVENT_LISTENERS),
                $this->collection->getProjectors(get_class($event->getParam(EventBus::EVENT_PARAM_MESSAGE)))
            )
        );
    }
}
