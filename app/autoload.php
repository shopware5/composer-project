<?php

use Dotenv\Dotenv;
use Composer\Autoload\ClassLoader;
use Doctrine\Common\Annotations\AnnotationRegistry;

/**
 * @var ClassLoader $loader
 */
$loader = require __DIR__.'/../vendor/autoload.php';

AnnotationRegistry::registerLoader([$loader, 'loadClass']);

if (file_exists(__DIR__ . '/../.env')) {
    $dotenv = Dotenv::create(__DIR__ . '/../');
    $dotenv->load();
}

return $loader;
