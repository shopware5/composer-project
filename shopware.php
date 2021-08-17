<?php

use Shopware\Components\HttpCache\AppCache;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\TerminableInterface;

/** @var Composer\Autoload\ClassLoader */
$loader = require __DIR__.'/app/autoload.php';

$environment = $_SERVER['SHOPWARE_ENV'] ?? 'production';
$kernel = new AppKernel($environment, $environment !== 'production');
if ($kernel->isHttpCacheEnabled()) {
    $kernel = new AppCache($kernel, $kernel->getHttpCacheConfig());
}

$request = Request::createFromGlobals();

$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
