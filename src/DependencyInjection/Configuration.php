<?php

namespace Nixilla\CqrsBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('nixilla_cqrs');

        $rootNode
            ->children()
                ->arrayNode('event_store')
                    ->children()
                        ->scalarNode('adapter')->end()
                    ->end()
                ->end() // event_store
            ->end()
        ;

        return $treeBuilder;
    }
}
