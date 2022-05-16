<?php

declare(strict_types=1);

namespace App\Component\Core;

use RuntimeException;
use Symfony\Component\HttpKernel\KernelInterface;

class HashValidator
{
    private KernelInterface $kernel;

    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    public function isValidHash(string $hash, string $text, string $envPasswords): bool
    {
        $passwords = $this->getEnv($envPasswords);

        if (!is_array($passwords)) {
            throw new RuntimeException('Passwords is not set');
        }

        foreach ($passwords as $password) {
            if ($hash === md5(trim($password) . $text)) {
                return true;
            }
        }

        return false;
    }

    private function getEnv(string $env)
    {
        return $this->kernel->getContainer()->getParameter($env);
    }
}
