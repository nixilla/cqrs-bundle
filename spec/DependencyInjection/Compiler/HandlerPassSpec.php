<?php

namespace spec\Nixilla\CqrsBundle\DependencyInjection\Compiler;

use Nixilla\CqrsBundle\DependencyInjection\Compiler\HandlerPass;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

class HandlerPassSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(HandlerPass::class);
        $this->shouldHaveType(CompilerPassInterface::class);
    }
}
