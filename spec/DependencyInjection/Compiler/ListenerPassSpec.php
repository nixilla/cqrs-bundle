<?php

namespace spec\Nixilla\CqrsBundle\DependencyInjection\Compiler;

use Nixilla\CqrsBundle\DependencyInjection\Compiler\ListenerPass;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class ListenerPassSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(ListenerPass::class);
        $this->shouldHaveType(CompilerPassInterface::class);
    }

    function it_registers_all_services_tagged_as_listeners_to_symfony_container(ContainerBuilder $builder, Definition $definition)
    {
        $builder->has('cqrs.listener.collection')->willReturn(false);
        $this->process($builder);

        $builder->has('cqrs.listener.collection')->willReturn(true);
        $builder->findDefinition('cqrs.listener.collection')->willReturn($definition);

        $services = [ 'someId' => [ ['event' => 'some.event' ]]];

        $builder->findTaggedServiceIds('cqrs.event.listener')->willReturn($services);

        $definition->addMethodCall('addListener', Argument::any())->shouldBeCalled();

        $this->process($builder);
    }
}
