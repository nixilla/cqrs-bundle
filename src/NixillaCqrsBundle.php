<?php

namespace Nixilla\CqrsBundle;

use Nixilla\CqrsBundle\DependencyInjection\Compiler\HandlerPass;
use Nixilla\CqrsBundle\DependencyInjection\Compiler\ListenerPass;
use Nixilla\CqrsBundle\DependencyInjection\Compiler\ProjectorPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class NixillaCqrsBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new HandlerPass());
        $container->addCompilerPass(new ProjectorPass());
        $container->addCompilerPass(new ListenerPass());
    }
}
