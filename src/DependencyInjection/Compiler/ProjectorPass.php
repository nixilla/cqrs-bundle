<?php

namespace Nixilla\CqrsBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ProjectorPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if( ! $container->has('cqrs.projector.collection')) return;

        $definition = $container->findDefinition('cqrs.projector.collection');

        $taggedServices = $container->findTaggedServiceIds('cqrs.event.projector');

        foreach ($taggedServices as $id => $tags)
        {
            foreach ($tags as $attributes)
            {
                $definition->addMethodCall('addProjector', [new Reference($id), $attributes['event']]);
            }
        }
    }
}
