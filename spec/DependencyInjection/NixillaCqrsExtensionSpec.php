<?php

namespace spec\Nixilla\CqrsBundle\DependencyInjection;

use Nixilla\CqrsBundle\DependencyInjection\NixillaCqrsExtension;
use PhpSpec\ObjectBehavior;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class NixillaCqrsExtensionSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(NixillaCqrsExtension::class);
        $this->shouldHaveType(Extension::class);
    }

    function it_loads_local_services_into_DI(ContainerBuilder $container)
    {
        $this->load($configs = [], $container)->shouldReturn(null);
    }
}
