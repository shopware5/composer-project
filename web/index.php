<?php

use Shopware\Components\HttpCache\AppCache;
use Symfony\Component\HttpFoundation\Request;

/**
 * @var Composer\Autoload\ClassLoader
 */
$loader = require __DIR__.'/../app/autoload.php';

$environment = getenv('SHOPWARE_ENV');
$kernel = new AppKernel($environment, $environment !== 'production');
if ($kernel->isHttpCacheEnabled()) {
    $kernel = new AppCache($kernel, $kernel->getHttpCacheConfig());
}

$request = Request::createFromGlobals();

// Trust the heroku load balancer
// see: https://devcenter.heroku.com/articles/getting-started-with-symfony#trusting-the-load-balancer
Request::setTrustedProxies([$request->server->get('REMOTE_ADDR')]);
Request::setTrustedHeaderName(Request::HEADER_FORWARDED, null);

$response = $kernel->handle($request);
$response->send();
