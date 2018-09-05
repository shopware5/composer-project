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
            list($version, $sha) = explode('@', \PackageVersions\Versions::getVersion('shopware/shopware'));

            /*
             * Trim leading v from versions like "v5.4.6"
             */
            $version = ltrim(strtolower($version), 'v');

            /*
             * Make sure the version matches some expected patterns like "5.4.0" or "5.0.0-RC1"
             */
            if (!preg_match('/^([\d]+\.[\d]+\.[\d]+(\-[a-zA-Z\d]{0,4})?)$/', $version)) {
                throw new OutOfBoundsException(sprintf('Version "%s" not in expected format', $version));
            }

            $this->release['version'] = $version;
            $this->release['revision'] = substr($sha, 0, 10);
            $this->release['version_text'] = ''; // Feel free to change this
        } catch (\OutOfBoundsException $ex) {
            // Silent catch
            $this->release['version'] = 'unknown';
            $this->release['revision'] = 'unknown';
            $this->release['version_text'] = '';
        }
    }
}
