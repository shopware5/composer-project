<?php declare(strict_types=1);

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Console\DependencyInjection\AddConsoleCommandPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

class BuildKernel extends AppKernel
{
    /**
     * @var bool
     */
    private $skipDatabase = false;

    public function boot($skipDatabase = false)
    {
        if ($this->booted) {
            return;
        }

        $this->skipDatabase = (bool) $skipDatabase;
        parent::boot($skipDatabase);
    }

    public function reboot(bool $skipDatabase = false)
    {
        $this->booted = false;
        $this->initializeConfig();
        $this->boot($skipDatabase);
    }

    /**
     * @return bool
     */
    public function isSkipDatabase()
    {
        return (bool) $this->skipDatabase;
    }

    protected function getKernelParameters()
    {
        return array_merge(parent::getKernelParameters(), [
            'build.root_dir' => dirname(dirname(__DIR__)),
        ]);
    }

    protected function prepareContainer(ContainerBuilder $container)
    {
        if ($this->skipDatabase) {
            $container->addCompilerPass(new AddConsoleCommandPass());
        } else {
            parent::prepareContainer($container);
        }

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/DependencyInjection/'));
        $loader->load('commands.xml');
    }

    protected function initializePlugins()
    {
        // The build application does not support plugins
    }

    protected function getContainerClass()
    {
        // Make name of container class unique, so multiple containers can be spawned (necessary for kernel reboot)
        return parent::getContainerClass() . uniqid();
    }
}
