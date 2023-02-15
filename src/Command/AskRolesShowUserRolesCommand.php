<?php

namespace App\Command;

use App\Repository\UserRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'ask:roles:show-user-roles',
    description: 'Shows list of the user roles',
)]
class AskRolesShowUserRolesCommand extends Command
{
    public function __construct(
        private UserRepository $userRepository,
        string $name = null
    ) {
        parent::__construct($name);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $questionHelper = $this->getHelper('question');
        $userIdQuestion = new Question('User id: ');

        $user = null;

        while ($user === null) {
            $userId = $questionHelper->ask($input, $output, $userIdQuestion);

            $user = $this->userRepository->find((int)$userId);

            if ($user === null) {
                $io->warning('User is not found by id: #' . $userId);
            }
        }

        foreach ($user->getRoles() as $role) {
            if ($role === 'ROLE_USER') {
                continue;
            }

            $io->text($role);
        }

        return Command::SUCCESS;
    }
}
