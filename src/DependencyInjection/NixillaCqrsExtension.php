<?php

namespace Nixilla\CqrsBundle\DependencyInjection;

use Prooph\EventStore\EventStore;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader;


class NixillaCqrsExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $this->processConfiguration($configuration, $configs);

        $def = new Definition(EventStore::class, [
            new Reference($configs[0]['event_store']['adapter']),
            new Reference('prooph.action.event.emitter')
        ]);

        $container->setDefinition('prooph.event.store', $def);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config/di'));

        $loader->load('services.yml');
        $loader->load('prooph.yml');
    }
}
