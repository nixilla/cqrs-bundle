<?php

namespace Nixilla\CqrsBundle\Services;

use Prooph\Common\Event\ActionEvent;
use Prooph\Common\Event\ActionEventEmitter;
use Prooph\Common\Event\ActionEventListenerAggregate;
use Prooph\ServiceBus\MessageBus;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;

class HandlerResolver implements ActionEventListenerAggregate
{
    /** @var ContainerInterface */
    private $container;

    /** @var NameConverterInterface */
    private $converter;

    /**
     * HandlerResolver constructor.
     *
     * @param ContainerInterface $container
     * @param NameConverterInterface $converter
     */
    public function __construct(ContainerInterface $container, NameConverterInterface $converter)
    {
        $this->container = $container;
        $this->converter = $converter;
    }

    /**
     * @param ActionEventEmitter $dispatcher
     */
    public function attach(ActionEventEmitter $dispatcher)
    {
        $dispatcher->attachListener(MessageBus::EVENT_ROUTE, [$this, 'onRoute']);
        $dispatcher->attachListener(MessageBus::EVENT_LOCATE_HANDLER, [$this, 'onLocateMessageHandler']);
    }

    /**
     * @param ActionEventEmitter $dispatcher
     */
    public function detach(ActionEventEmitter $dispatcher)
    {
        throw new \BadMethodCallException('Not implemented');
    }

    /**
     * Sets identifier for handler service.
     *
     *   Domain\Command\RegisterContact will be changed to listener_register_contact
     *
     * @param ActionEvent $event
     */
    public function onRoute(ActionEvent $event)
    {
        $name = $event->getParam(MessageBus::EVENT_PARAM_MESSAGE_NAME);
        $event->setParam(
            MessageBus::EVENT_PARAM_MESSAGE_HANDLER,
            $this->converter->normalize(lcfirst(substr($name, strrpos($name, '\\') + 1)))
        );
    }

    /**
     * Find all listeners for given event handler name
     *
     * @param ActionEvent $event
     */
    public function onLocateMessageHandler(ActionEvent $event)
    {
        $messageHandlerAlias = $event->getParam(MessageBus::EVENT_PARAM_MESSAGE_HANDLER);

        if (is_string($messageHandlerAlias))
        {
            $handler = $this->container->get(sprintf('handler.%s', $messageHandlerAlias));

            $event->setParam(MessageBus::EVENT_PARAM_MESSAGE_HANDLER, $handler);
        }
    }
}