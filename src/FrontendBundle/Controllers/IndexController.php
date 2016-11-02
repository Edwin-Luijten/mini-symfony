<?php

namespace FrontendBundle\Controllers;

use Symfony\Component\HttpFoundation\Response;

class IndexController
{
    public function getWelcome() {
        return new Response(
            file_get_contents(__DIR__ . '/../Resources/views/welcome.html')
        );
    }
}