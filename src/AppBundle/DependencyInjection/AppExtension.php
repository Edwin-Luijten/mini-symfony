<?php

namespace AppBundle\DependencyInjection;

use AppBundle\DependencyInjection\Compilers\ContainerBuilderDebugDumpPass;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\LogicException;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * FrameworkExtension.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Jeremy Mikola <jmikola@gmail.com>
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
class AppExtension extends Extension
{
    /**
     * Responds to the app.config configuration parameter.
     *
     * @param array            $configs
     * @param ContainerBuilder $container
     *
     * @throws LogicException
     */
    public function load(array $configs, ContainerBuilder $container)
    {

    }
}
