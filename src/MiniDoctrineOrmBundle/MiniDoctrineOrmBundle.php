<?php

namespace MiniDoctrineOrmBundle;

use MiniDoctrineOrmBundle\DependencyInjection\MiniDoctrineOrmExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class MiniDoctrineOrmBundle extends Bundle
{
    public function getContainerExtension()
    {
        return new MiniDoctrineOrmExtension();
    }
}
