<?php

use PackageVersions\Versions;
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

        return parent::prepareContainer($container);
    }

    private function loadRelease()
    {
        $this->loadReleaseFromEnv();
        $this->loadReleaseFromComposer();
    }

    /**
     * Setting the environment variables, either directly, by the webserver or using the .env-file
     * allows you to define a custom Shopware version IF NECESSARY.
     *
     * It should match the version being installed by composer. This way plugins still are able to check
     * for the Shopware version.
     *
     * YOU SHOULDN'T NORMALLY HAVE TO DO THIS! (See below)
     */
    private function loadReleaseFromEnv()
    {
        $this->release['version'] = getenv('SHOPWARE_VERSION') === false ? self::VERSION : getenv('SHOPWARE_VERSION');
        $this->release['revision'] = getenv('SHOPWARE_REVISION') === false ? self::REVISION : getenv('SHOPWARE_REVISION');
        $this->release['version_text'] = getenv('SHOPWARE_VERSION_TEXT') === false ? self::VERSION_TEXT : getenv('SHOPWARE_VERSION_TEXT');
    }

    /**
     * We try to determine the installed version of Shopware automatically.
     */
    private function loadReleaseFromComposer()
    {
        // If something was defined in the ENV, we respect that setting
        if ($this->release['version'] !== self::VERSION) {
            return;
        }

        try {
            $version = Versions::getVersion('shopware/shopware');

            if (!preg_match('/^v?(?<plainVersion>[\d]+\.[\d]+\.[\d]+)(\-(?<stability>[a-z\d]{0,4}))?(@(?<hash>[a-z\d]+)?)?$/i', $version, $versionMatches)) {
                throw new OutOfBoundsException(sprintf('Version "%s" not in expected format', $version));
            }

            $this->release['version'] = $versionMatches['plainVersion'];
            $this->release['revision'] = isset($versionMatches['hash']) ? substr($versionMatches['hash'], 0, 10) : '';
            $this->release['version_text'] = $versionMatches['stability'] ?? '';
        } catch (\OutOfBoundsException $ex) {
            // Silent catch
            $this->release['version'] = 'unknown';
            $this->release['revision'] = 'unknown';
            $this->release['version_text'] = '';
        }
    }
}
