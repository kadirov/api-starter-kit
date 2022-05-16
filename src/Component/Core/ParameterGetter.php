<?php

declare(strict_types=1);

namespace App\Component\Core;

use Psr\Container\ContainerInterface;

class ParameterGetter
{
    public function __construct(private ContainerInterface $parameterBag)
    {
    }

    public function get(string $name): mixed
    {
        return $this->parameterBag->getParameter($name);
    }

    public function getString(string $name): string
    {
        return (string)$this->get($name);
    }

    public function getInt(string $name): int
    {
        return (int)$this->get($name);
    }

    public function getArray(string $name): array
    {
        return (array)$this->get($name);
    }

    public function getBool(string $name): bool
    {
        return (bool)$this->get($name);
    }

    public function getFloat(string $name): float
    {
        return (float)$this->get($name);
    }
}
