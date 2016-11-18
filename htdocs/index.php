<?php

use Symfony\Component\Debug\Debug;
use Symfony\Component\HttpFoundation\Request;

require_once __DIR__ . '/../app/autoload.php';

$debug = false;
if (ENVIRONMENT !== ENV_PRODUCTION) {
    $debug = true;
    Debug::enable();
}


$kernel = new AppKernel(ENVIRONMENT, $debug);
$kernel->loadClassCache();

Request::enableHttpMethodParameterOverride();

$request = Request::createFromGlobals();

$response = $kernel->handle($request);
$response->send();

$kernel->terminate($request, $response);
