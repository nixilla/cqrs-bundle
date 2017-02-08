<?php

namespace Nixilla\CqrsBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ListenerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if( ! $container->has('cqrs.listener.list')) return;

        $definition = $container->findDefinition('cqrs.listener.list');

        $taggedServices = $container->findTaggedServiceIds('cqrs.event.listener');

        foreach ($taggedServices as $id => $tags)
        {
            foreach ($tags as $attributes)
            {
                $definition->addMethodCall('addListener', [new Reference($id), $attributes['event']]);
            }
        }
    }
}
