<?php

declare(strict_types=1);

namespace App\Component\User\Dtos;

use Lexik\Bundle\JWTAuthenticationBundle\Security\User\JWTUserInterface;

class JwtUserDto implements JWTUserInterface
{
    private int $id;
    private string $email;
    private array $roles;

    public function __construct(int $id, string $email, array $roles)
    {
        $this->id = $id;
        $this->email = $email;
        $this->roles = $roles;
    }

    public static function createFromPayload($username, array $payload): JwtUserDto
    {
        return new self(
            $payload['id'],
            $username,
            $payload['roles']
        );
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function getPassword()
    {
    }

    public function getSalt()
    {
    }

    public function getUsername(): string
    {
        return $this->getEmail();
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function eraseCredentials(): void
    {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUserIdentifier(): string
    {
        return (string)$this->getId();
    }
}
