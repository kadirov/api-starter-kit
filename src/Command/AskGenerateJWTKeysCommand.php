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

#[AsCommand(
    name: 'ask:generate:jwtKeys',
    description: 'Generate jwt keys. If keys are exist they are will be dropped',
)]
class AskGenerateJWTKeysCommand extends Command implements GetOutputInterface
{
    use RunCommandTrait;

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

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ): int {
        $this->symfonyIO = new SymfonyStyle($input, $output);
        $this->output = $output;

        $this->createJwtFolder();
        $this->createPassphrase();
        $this->allowAccessToPrivateKey();

        return Command::SUCCESS;
    }

    private function createJwtFolder(): void
    {
        $this->runSystemCommandAndNotify(
            'mkdir -p config/jwt',
            'Created config/jwt folder',
            'Could not create folder config/jwt'
        );
    }

    private function createPassphrase(): void
    {
        $this->runSystemCommandAndNotify(
            '
            jwt_passphrase=${JWT_PASSPHRASE:-$(grep \'\'^JWT_PASSPHRASE=\'\' .env | cut -f 2 -d \'\'=\'\')}
            echo "$jwt_passphrase" | openssl genpkey -out config/jwt/private.pem -pass stdin -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096
            echo "$jwt_passphrase" | openssl pkey -in config/jwt/private.pem -passin stdin -out config/jwt/public.pem -pubout
            ',
            'JWT keys are created',
            'Failed on creating JWT keys'
        );
    }

    private function allowAccessToPrivateKey(): void
    {
        $this->runSystemCommandAndNotify('chmod 0644 config/jwt/private.pem');
    }
}
