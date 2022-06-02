<?php

declare(strict_types=1);

namespace App\Component\Core;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

class ParameterGetter
{
    public function __construct(private ContainerBagInterface $params)
    {
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function get(string $name): mixed
    {
        return $this->params->get($name);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getString(string $name): string
    {
        return (string)$this->get($name);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getInt(string $name): int
    {
        return (int)$this->get($name);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getArray(string $name): array
    {
        return (array)$this->get($name);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getBool(string $name): bool
    {
        return (bool)$this->get($name);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getFloat(string $name): float
    {
        return (float)$this->get($name);
    }
}
