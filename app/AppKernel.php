<?php

use Shopware\Kernel;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

class AppKernel extends Kernel
{
    /**
     * @param string $environment
     * @param bool   $debug
     *
     * @throws \Exception
     */
    public function __construct($environment, $debug)
    {
        $this->loadRelease();
        parent::__construct($environment, $debug);
    }

    protected function initializeShopware()
    {
        $this->shopware = new Application($this->container);
        $this->container->setApplication($this->shopware);
    }

    /**
     * @return string
     */
    protected function getConfigPath()
    {
        return __DIR__ . '/config/config.php';
    }

    /**
     * @return string
     */
    public function getCacheDir()
    {
        return dirname(__DIR__) . '/var/cache/' . $this->getEnvironment() . '_' . $this->release['revision'];
    }

    /**
     * Gets the log directory.
     *
     * @return string The log directory
     */
    public function getLogDir()
    {
        return dirname(__DIR__) . '/var/log';
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

        parent::prepareContainer($container);
    }

    private function loadRelease(): void
    {
        try {
            $release = ShopwareVersion::parseVersion();
        } catch (\OutOfBoundsException $ex) {
            $release = [
                'version' => $_SERVER['SHOPWARE_VERSION'] ?? '___VERSION___',
                'version_text' => $_SERVER['SHOPWARE_VERSION_TEXT'] ?? '___VERSION_TEXT___',
                'revision' => $_SERVER['SHOPWARE_REVISION'] ?? '___REVISION___',
            ];
        }

        $this->release = $release;
    }
}
