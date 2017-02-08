<?php

namespace Nixilla\CqrsBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class HandlerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if( ! $container->has('cqrs.handler.list')) return;

        $definition = $container->findDefinition('cqrs.handler.list');

        $taggedServices = $container->findTaggedServiceIds('cqrs.command.handler');

        foreach ($taggedServices as $id => $tags)
        {
            foreach ($tags as $attributes)
            {
                $definition->addMethodCall('setHandler', array(new Reference($id), $attributes['event']));
            }
        }
    }
}
