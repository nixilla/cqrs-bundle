<?php

namespace spec\Nixilla\CqrsBundle\DependencyInjection\Compiler;

use Nixilla\CqrsBundle\DependencyInjection\Compiler\ProjectorPass;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class ProjectorPassSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(ProjectorPass::class);
        $this->shouldHaveType(CompilerPassInterface::class);
    }

    function it_registers_all_services_tagged_as_listeners_to_symfony_container(ContainerBuilder $builder, Definition $definition)
    {
        $builder->has('cqrs.projector.collection')->willReturn(false);
        $this->process($builder);

        $builder->has('cqrs.projector.collection')->willReturn(true);
        $builder->findDefinition('cqrs.projector.collection')->willReturn($definition);

        $services = [ 'someId' => [ ['event' => 'some.event' ]]];

        $builder->findTaggedServiceIds('cqrs.event.projector')->willReturn($services);

        $definition->addMethodCall('addProjector', Argument::any())->shouldBeCalled();

        $this->process($builder);
    }
}
