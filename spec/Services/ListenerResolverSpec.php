<?php

namespace spec\Nixilla\CqrsBundle\Services;

use Nixilla\CqrsBundle\Services\ListenerCollection;
use Nixilla\CqrsBundle\Services\ListenerResolver;
use PhpSpec\ObjectBehavior;
use Prooph\Common\Event\ActionEvent;
use Prooph\Common\Event\ActionEventEmitter;
use Prophecy\Argument;

class ListenerResolverSpec extends ObjectBehavior
{
    function let(ListenerCollection $collection)
    {
        $this->beConstructedWith($collection);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ListenerResolver::class);
    }

    function it_can_attach_itself_to_dispatcher(ActionEventEmitter $dispatcher)
    {
        $dispatcher
            ->attachListener(Argument::any(), Argument::any())
            ->shouldBeCalled();

        $this->attach($dispatcher);
    }

    function it_detach_is_not_implemented(ActionEventEmitter $dispatcher)
    {
        $this
            ->shouldThrow('\BadMethodCallException')
            ->during('detach', [$dispatcher])
        ;
    }

    function it_finds_listeners_for_given_event(ActionEvent $event)
    {
        $event
            ->setParam(Argument::any(), Argument::any())
            ->shouldBeCalled();

        $event
            ->getParam(Argument::any())
            ->shouldBeCalled();

        $this->onRoute($event);
    }
}
