<?php
declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class MyDeployCommand extends Command
{
    protected static $defaultName = 'my:deploy';

    private OutputInterface $output;
    private SymfonyStyle $symfonyIO;

    protected function configure()
    {
        $this->setDescription("Call this everytime after 'git pull' or create git hook by: cp docker/other-files/git/hooks/post-merge .git/hooks");
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ): int {
        $this->symfonyIO = new SymfonyStyle($input, $output);
        $this->output = $output;

        $this->changeOwner();
        $this->runMigrations();
        $this->clearCache();

        return 0;
    }

    private function changeOwner(): void
    {
        if (system('chown -R www-data:www-data ./var') === false) {
            $this->symfonyIO->error('Could not change owner of "./var" to www-data.');
        } else {
            $this->symfonyIO->success('Changed owner of "./var" to www-data.');
        }

        if (system('chown -R www-data:www-data ./public/media') === false) {
            $this->symfonyIO->error('Could not change owner of "./public/media" to www-data.');
        } else {
            $this->symfonyIO->success('Changed owner of "./public/media" to www-data.');
        }
    }

    private function clearCache(): void
    {
        if ($this->runCommand('cache:clear') === 0) {
            $this->symfonyIO->success('Cache is cleared');
        } else {
            $this->symfonyIO->error('Could not clear cache');
        }
    }

    private function runMigrations(): void
    {
        $args = [
            '--allow-no-migration' => true,
            '--no-interaction'     => true,
        ];

        if ($this->runCommand('doctrine:migrations:migrate', $args) === 0) {
            $this->symfonyIO->success('Migrations applied');
        } else {
            $this->symfonyIO->error('Could not apply migrations');
        }
    }

    private function findCommand(string $name): Command
    {
        return $this->getApplication()->find($name);
    }

    private function runCommand(string $name, array $arguments = []): int
    {
        $args = new ArrayInput($arguments);
        $args->setInteractive(false);

        return $this
            ->findCommand($name)
            ->run($args, $this->output);
    }
}
