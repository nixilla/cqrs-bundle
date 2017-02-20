<?php

namespace spec\Nixilla\CqrsBundle\Services;

use Nixilla\CqrsBundle\Services\ProjectorCollection;
use Nixilla\CqrsBundle\Services\ProjectorResolver;
use PhpSpec\ObjectBehavior;
use Prooph\Common\Event\ActionEvent;
use Prooph\Common\Event\ActionEventEmitter;
use Prooph\ServiceBus\EventBus;
use Prophecy\Argument;

class ProjectorResolverSpec extends ObjectBehavior
{
    function let(ProjectorCollection $collection)
    {
        $this->beConstructedWith($collection);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ProjectorResolver::class);
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

    function it_finds_listeners_for_given_event(ActionEvent $event, ProjectorCollection $collection)
    {
        $event
            ->setParam(Argument::any(), Argument::any())
            ->shouldBeCalled();

        $event
            ->getParam(EventBus::EVENT_PARAM_EVENT_LISTENERS)
            ->willReturn([]);

        $event
            ->getParam(EventBus::EVENT_PARAM_MESSAGE)
            ->willReturn(new \stdClass());

        $collection->getProjectors(Argument::any())->willReturn([]);

        $this->onRoute($event);
    }
}
