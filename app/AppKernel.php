<?php

use Shopware\Kernel;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

class AppKernel extends Kernel
{
    /**
     * @return string
     */
    protected function getConfigPath()
    {
        return __DIR__.'/config/config.php';
    }

    /**
     * @return string
     */
    public function getCacheDir()
    {
        return dirname(__DIR__).'/var/cache/'.$this->getEnvironment();
    }

    /**
     * Gets the log directory.
     *
     * @return string The log directory
     */
    public function getLogDir()
    {
        return dirname(__DIR__).'/var/log';
    }

    /**
     * @param ContainerBuilder $container
     *
     * @throws Exception
     */
    protected function prepareContainer(ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__));
        $loader->load('services.xml');

        return parent::prepareContainer($container);
    }

    /**
     * @param string $environment
     * @param bool   $debug
     *
     * @throws \Exception
     */
    public function __construct($environment, $debug)
    {
        /**
         * Setting the environment variables using the .env-file allows to define a custom Shopware version.
         * It should match the version being installed by composer.
         * This way plugins still are able to check for the Shopware version.
         */
        $this->release['version'] = getenv('SHOPWARE_VERSION') ?: self::VERSION;
        $this->release['version_text'] = getenv('SHOPWARE_VERSION_TEXT') ?: self::VERSION_TEXT;
        $this->release['revision'] = getenv('SHOPWARE_REVISION') ?: self::REVISION;

        parent::__construct($environment, $debug);
    }
}
