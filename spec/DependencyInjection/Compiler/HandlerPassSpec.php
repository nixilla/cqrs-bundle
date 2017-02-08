<?php

namespace spec\Nixilla\CqrsBundle\DependencyInjection\Compiler;

use Nixilla\CqrsBundle\DependencyInjection\Compiler\HandlerPass;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class HandlerPassSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(HandlerPass::class);
        $this->shouldHaveType(CompilerPassInterface::class);
    }

    function it_registers_all_services_tagged_as_handler_to_symfony_container(ContainerBuilder $builder, Definition $definition)
    {
        $builder->has('cqrs.handler.collection')->willReturn(false);
        $this->process($builder);

        $builder->has('cqrs.handler.collection')->willReturn(true);
        $builder->findDefinition('cqrs.handler.collection')->willReturn($definition);

        $services = [ 'someId' => [ ['event' => 'some.event' ]]];

        $builder->findTaggedServiceIds('cqrs.command.handler')->willReturn($services);

        $definition->addMethodCall('setHandler', Argument::any())->shouldBeCalled();

        $this->process($builder);
    }
}
