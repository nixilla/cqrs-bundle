<?php

namespace Nixilla\CqrsBundle\Services;

use Prooph\Common\Event\ActionEvent;
use Prooph\Common\Event\ActionEventEmitter;
use Prooph\ServiceBus\EventBus;
use Prooph\ServiceBus\MessageBus;

class ListenerResolver
{
    /** @var ListenerCollection */
    private $collection;

    /**
     * ListenerResolver constructor.
     * @param ListenerCollection $collection
     */
    public function __construct(ListenerCollection $collection)
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
        $event->setParam(EventBus::EVENT_PARAM_EVENT_LISTENERS, $this->collection->getListeners(get_class($event->getParam(EventBus::EVENT_PARAM_MESSAGE))));
    }
}
