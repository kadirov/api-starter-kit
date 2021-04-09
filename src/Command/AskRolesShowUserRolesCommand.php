<?php

namespace App\Command;

use App\Component\User\UserManager;
use App\Repository\UserRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

class AskRolesShowUserRolesCommand extends Command
{
    protected static $defaultName = 'ask:roles:show-user-roles';
    private UserRepository $userRepository;
    private UserManager $userManager;

    public function __construct(
        UserRepository $userRepository,
        UserManager $userManager,
        string $name = null
    ) {
        parent::__construct($name);
        $this->userRepository = $userRepository;
        $this->userManager = $userManager;
    }

    protected function configure(): void
    {
        $this->setDescription('Shows list of the user roles');
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
            $io->text($role);
        }

        return 0;
    }
}
