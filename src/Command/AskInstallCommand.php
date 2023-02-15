<?php

declare(strict_types=1);

namespace App\Command;

use App\Command\Interfaces\GetOutputInterface;
use App\Command\Traits\RunCommandTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Throwable;

#[AsCommand(
    name: 'ask:install',
    description: 'Do first settings for Api-Starter-Kit',
)]
class AskInstallCommand extends Command implements GetOutputInterface
{
    use RunCommandTrait;

    private OutputInterface $output;
    private SymfonyStyle $symfonyIO;

    public function __construct(
        private EntityManagerInterface $entityManager,
        string $name = null
    ) {
        parent::__construct($name);
    }

    public function getOutput(): OutputInterface
    {
        return $this->output;
    }

    public function getSymfonyStyleOutput(): SymfonyStyle
    {
        return $this->symfonyIO;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->symfonyIO = new SymfonyStyle($input, $output);
        $this->output = $output;

        $this->waitConnection();
        $this->runCommandAndNotify('ask:deploy');
        $this->runCommandAndNotify('ask:generate:jwtKeys');

        $this->symfonyIO->success('Successfully installed');

        return Command::SUCCESS;
    }

    private function waitConnection(): void
    {
        $this->symfonyIO->writeln("Try to connect to database");
        $this->symfonyIO->writeln("Please, be patiently. First running of MySQL server requires much time");

        while (true) {
            try {
                $this->entityManager->getConnection()->connect();
                if ($this->entityManager->getConnection()->isConnected()) {
                    return;
                }
            } catch (Throwable) {
                sleep(5);
            }
        }
    }
}
