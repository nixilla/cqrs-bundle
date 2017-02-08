<?php

namespace spec\Nixilla\CqrsBundle\Services;

use Nixilla\CqrsBundle\Services\HandlerResolver;
use PhpSpec\ObjectBehavior;
use Prooph\Common\Event\ActionEvent;
use Prooph\Common\Event\ActionEventEmitter;
use Prophecy\Argument;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;

class HandlerResolverSpec extends ObjectBehavior
{
    function let(ContainerInterface $container, NameConverterInterface $nameConverter)
    {
        $this->beConstructedWith($container, $nameConverter);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(HandlerResolver::class);
    }

    function it_can_attach_itself_to_arbitrary_events(ActionEventEmitter $dispatcher)
    {
        $dispatcher
            ->attachListener(Argument::any(), Argument::any())
            ->shouldBeCalled();

        $this->attach($dispatcher)->shouldReturn(null);
    }

    function it_detach_is_not_implemented(ActionEventEmitter $dispatcher)
    {
        $this
            ->shouldThrow('\BadMethodCallException')
            ->during('detach', [$dispatcher])
        ;
    }

    function it_assigns_route_handler(ActionEvent $event, NameConverterInterface $nameConverter)
    {
        $event->getParam(Argument::any())->shouldBeCalled();
        $event->setParam(Argument::any(), Argument::any())->shouldBeCalled();
        $nameConverter->normalize(Argument::any())->shouldBeCalled();

        $this->onRoute($event)->shouldReturn(null);
    }

    function it_locates_route_handler(ActionEvent $event, ContainerInterface $container)
    {
        $event->getParam(Argument::any())->willReturn('some.service.tag');
        $event->setParam(Argument::any(), Argument::any())->shouldBeCalled();

        $container->get(Argument::any())->shouldBeCalled();

        $this->onLocateMessageHandler($event)->shouldReturn(null);
    }
}
