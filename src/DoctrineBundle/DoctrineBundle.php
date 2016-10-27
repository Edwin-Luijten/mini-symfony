<?php

namespace DoctrineBundle;

use DoctrineBundle\DependencyInjection\DoctrineExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class DoctrineBundle extends Bundle
{
    public function getContainerExtension()
    {
        return new DoctrineExtension();
    }
}
