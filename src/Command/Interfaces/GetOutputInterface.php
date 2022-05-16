<?php declare(strict_types=1);

namespace App\Command\Interfaces;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

interface GetOutputInterface
{
    public function getOutput(): OutputInterface;
    public function getSymfonyStyleOutput(): SymfonyStyle;
}
