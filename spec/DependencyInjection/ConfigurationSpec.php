<?php

namespace spec\Nixilla\CqrsBundle\DependencyInjection;

use Nixilla\CqrsBundle\DependencyInjection\Configuration;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class ConfigurationSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(Configuration::class);
        $this->shouldHaveType(ConfigurationInterface::class);
    }

    function it_implements_required_method()
    {
        $this->getConfigTreeBuilder()->shouldReturnAnInstanceOf(TreeBuilder::class);
    }
}
