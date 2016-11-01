<?php

namespace MiniDoctrineBundle;

use MiniDoctrineBundle\DependencyInjection\MiniDoctrineExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class MiniDoctrineBundle extends Bundle
{
    public function getContainerExtension()
    {
        return new MiniDoctrineExtension();
    }
}
