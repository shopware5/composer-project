<?php declare(strict_types=1);

namespace Commands;

use BuildKernel;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use Shopware\Components\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

abstract class BuildCommand extends Command
{
    /**
     * @var Filesystem
     */
    protected $filesystem;

    public function __construct(string $rootDir)
    {
        parent::__construct();

        $this->filesystem = new Filesystem(new Local($rootDir));
    }

    protected function envFileExists()
    {
        return $this->filesystem->has('.env');
    }

    protected function showBanner(OutputInterface $output)
    {
        $banner = '<info>' . PHP_EOL . $this->filesystem->read('app/bin/banner.txt') . PHP_EOL . '</info>';
        $output->writeln($banner);
    }

    protected function createSymlinks()
    {
        /** @var Local $adapter */
        $adapter = $this->filesystem->getAdapter();

        $this->filesystem->deleteDir('engine/Library');
        $this->filesystem->createDir('engine/Library');

        symlink(
            $adapter->applyPathPrefix('vendor/shopware/shopware/engine/Library/CodeMirror'),
            $adapter->applyPathPrefix('engine/Library/CodeMirror')
        );

        symlink(
            $adapter->applyPathPrefix('vendor/shopware/shopware/engine/Library/ExtJs'),
            $adapter->applyPathPrefix('engine/Library/ExtJs')
        );

        symlink(
            $adapter->applyPathPrefix('vendor/shopware/shopware/engine/Library/TinyMce'),
            $adapter->applyPathPrefix('engine/Library/TinyMce')
        );

        unlink($adapter->applyPathPrefix('themes/Frontend/Bare'));
        unlink($adapter->applyPathPrefix('themes/Frontend/Responsive'));
        unlink($adapter->applyPathPrefix('themes/Backend/ExtJs'));

        $this->filesystem->createDir('themes/Frontend');
        $this->filesystem->createDir('themes/Backend');

        symlink(
            $adapter->applyPathPrefix('vendor/shopware/shopware/themes/Backend/ExtJs'),
            $adapter->applyPathPrefix('themes/Backend/ExtJs')
        );

        symlink(
            $adapter->applyPathPrefix('vendor/shopware/shopware/themes/Frontend/Bare'),
            $adapter->applyPathPrefix('themes/Frontend/Bare')
        );

        symlink(
            $adapter->applyPathPrefix('vendor/shopware/shopware/themes/Frontend/Responsive'),
            $adapter->applyPathPrefix('themes/Frontend/Responsive')
        );
    }

    protected function executeCommand(string $command, OutputInterface $output)
    {
        $input = new StringInput($command);
        $input->setInteractive(false);

        $io = new SymfonyStyle($input, $output);
        $io->comment($command);

        $this->getApplication()->setAutoExit(false);
        $this->getApplication()->run($input, $output);
        $this->getApplication()->setAutoExit(true);
    }

    protected function rebootApplication()
    {
        /** @var Application $application */
        $application = $this->getApplication();
        /** @var BuildKernel $kernel */
        $kernel = $application->getKernel();

        $kernel->reboot();

        $application = new Application($kernel);
        $this->setApplication($application);
    }
}
