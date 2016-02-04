<?php

use Doctrine\Common\Annotations\AnnotationRegistry;
use Composer\Autoload\ClassLoader;
use Dotenv\Dotenv;

/**
 * @var ClassLoader $loader
 */
$loader = require __DIR__.'/../vendor/autoload.php';

AnnotationRegistry::registerLoader([$loader, 'loadClass']);


if (file_exists(__DIR__ . '/../.env')) {
    $dotenv = new Dotenv(__DIR__ . '/../');
    $dotenv->load();
}

define('PUBLICDIR', dirname(__DIR__).'/web/');
define('PROJECTDIR', dirname(__DIR__));
define('FRONTENDTHEMEDIR', dirname(__DIR__).'/Themes');
define('PUBLICPATH', '');

return $loader;
