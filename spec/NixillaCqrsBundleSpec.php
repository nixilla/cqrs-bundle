<?php

namespace spec\Nixilla\CqrsBundle;

use Nixilla\CqrsBundle\NixillaCqrsBundle;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class NixillaCqrsBundleSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(NixillaCqrsBundle::class);
        $this->shouldHaveType(Bundle::class);
    }

    function it_registers_compiler_passes(ContainerBuilder $builder)
    {
        $builder->addCompilerPass(Argument::any())->shouldBeCalled();
        $this->build($builder);
    }
}
