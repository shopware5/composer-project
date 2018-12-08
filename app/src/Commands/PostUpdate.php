<?php declare(strict_types=1);

namespace Commands;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PostUpdate extends BuildCommand
{
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->showBanner($output);
        $output->writeln('Updating Shopware install, please wait...');
        $this->createSymlinks();

        $this->executeCommand('sw:migrations:migrate --mode=update', $output);
        $this->executeCommand('sw:cache:clear', $output);
        $this->rebootApplication();
        $this->executeCommand('sw:generate:attributes', $output);
        $this->executeCommand('sw:theme:cache:generate', $output);
        $this->executeCommand('sw:plugin:refresh', $output);
        $this->executeCommand('sw:plugin:update --batch=active', $output);
        $this->executeCommand('sw:snippets:to:db', $output);

        $output->writeln('Done!');
    }
}
