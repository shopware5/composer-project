<?php declare(strict_types=1);

use Shopware\Components\Console\Application;
use Symfony\Component\Console\Output\OutputInterface;

class BuildApplication extends Application
{
    protected function registerCommands(OutputInterface $output)
    {
        /** @var BuildKernel $kernel */
        $kernel = $this->getKernel();

        if ($kernel->isSkipDatabase()) {
            $this->registerTaggedServiceIds();
        } else {
            parent::registerCommands($output);
        }
    }
}
