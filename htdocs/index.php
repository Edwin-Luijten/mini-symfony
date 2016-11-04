<?php

use Symfony\Component\HttpFoundation\Request;

require_once __DIR__.'/../app/autoload.php';

$env = ENVIRONMENT ?: 'prod';
$debug = ('prod' !== $env);

if ($debug) {
    \Symfony\Component\Debug\Debug::enable();
}

$kernel = new AppKernel($env, $debug);
$kernel->loadClassCache();
Request::enableHttpMethodParameterOverride();
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
