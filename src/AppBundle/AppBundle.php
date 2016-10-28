<?php

namespace AppBundle;

use AppBundle\DependencyInjection\Compilers\ContainerBuilderDebugDumpPass;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class AppBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        if ($container->getParameter('kernel.debug')) {
            $container->addCompilerPass(new ContainerBuilderDebugDumpPass(), PassConfig::TYPE_AFTER_REMOVING);
        }

        parent::build($container);
    }
}
