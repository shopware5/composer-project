<?php declare(strict_types=1);

namespace Commands;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PostInstall extends BuildCommand
{
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->createSymlinks();

        if ($this->envFileExists()) {
            $this->executeCommand('sw:build:post-update', $output);
        } else {
            $this->showBanner($output);
            $output->writeln('Please run "bin/build sw:build:install" manually to finish your installation.');
            $output->writeln('Have a nice day!');
        }
    }
}
