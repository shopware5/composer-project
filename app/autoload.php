<?php

use Dotenv\Dotenv;
use Composer\Autoload\ClassLoader;
use Doctrine\Common\Annotations\AnnotationRegistry;

/**
 * @var ClassLoader $loader
 */
$loader = require __DIR__.'/../vendor/autoload.php';

AnnotationRegistry::registerLoader([$loader, 'loadClass']);

$shopwareEnv = preg_replace('/[^a-zA-Z0-9-_.]/', '', getenv('SHOPWARE_ENV'));
if (false === empty($shopwareEnv) && file_exists(__DIR__ . '/../.env.' . $shopwareEnv)) {
    $dotenv = new Dotenv(__DIR__ . '/../', '.env.' . $shopwareEnv);
    $dotenv->load();
}

if (file_exists(__DIR__ . '/../.env')) {
    $dotenv = new Dotenv(__DIR__ . '/../');
    $dotenv->overload();
}

return $loader;
