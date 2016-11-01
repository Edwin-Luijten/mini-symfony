<?php

namespace MiniDoctrineDbalBundle;

use MiniDoctrineDbalBundle\DependencyInjection\MiniDoctrineDbalExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class MiniDoctrineDbalBundle extends Bundle
{
    public function getContainerExtension()
    {
        return new MiniDoctrineDbalExtension();
    }
}
