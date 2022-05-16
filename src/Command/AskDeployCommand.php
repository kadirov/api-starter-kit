<?php
declare(strict_types=1);

namespace App\Command;

use App\Command\Interfaces\GetOutputInterface;
use App\Command\Traits\RunCommandTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class AskDeployCommand extends Command implements GetOutputInterface
{
    use RunCommandTrait;

    protected static $defaultName = 'ask:deploy';

    private OutputInterface $output;
    private SymfonyStyle $symfonyIO;

    public function getOutput(): OutputInterface
    {
        return $this->output;
    }

    public function getSymfonyStyleOutput(): SymfonyStyle
    {
        return $this->symfonyIO;
    }

    protected function configure(): void
    {
        $this->setDescription("Call this everytime after 'git pull' or create git hook by: cp docker/other-files/git/hooks/post-merge .git/hooks");
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ): int {
        $this->symfonyIO = new SymfonyStyle($input, $output);
        $this->output = $output;

        $this->composerInstall();
        $this->changeOwner();
        $this->runMigrations();
        $this->clearCache();

        return Command::SUCCESS;
    }

    private function composerInstall(): void
    {
        $this->runSystemCommandAndNotify('composer install');
    }

    private function changeOwner(): void
    {
        $this->runSystemCommandAndNotify(
            'chown -R www-data:www-data ./var',
            'Changed owner of "./var" to www-data',
            'Could not change owner of "./var" to www-data'
        );

        $this->runSystemCommandAndNotify(
            'chown -R www-data:www-data ./public/media',
            'Changed owner of "./public/media" to www-data',
            'Could not change owner of "./public/media" to www-data'
        );
    }

    private function runMigrations(): void
    {
        $args = [
            '--allow-no-migration' => true,
            '--no-interaction'     => true,
        ];

        $this->runCommandAndNotify('doctrine:migrations:migrate', $args);
    }

    private function clearCache(): void
    {
        $this->runCommandAndNotify('cache:clear');
    }
}
