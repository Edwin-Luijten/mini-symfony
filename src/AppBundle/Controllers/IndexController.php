<?php

namespace AppBundle\Controllers;

use Doctrine\DBAL\Connection;

class IndexController
{
    public function __construct(Connection $connection)
    {
        var_dump($connection->getDatabase());
    }

    public function getIndex()
    {
        die('pong');
    }
}