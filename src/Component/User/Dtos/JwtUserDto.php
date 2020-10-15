<?php declare(strict_types=1);

namespace App\Component\User\Dtos;

use Lexik\Bundle\JWTAuthenticationBundle\Security\User\JWTUserInterface;

class JwtUserDto implements JWTUserInterface
{
    private int $id;
    private string $email;
    private array $roles;
    private int $appId;

    public function __construct(int $id, string $email, array $roles, int $appId)
    {
        $this->id = $id;
        $this->email = $email;
        $this->roles = $roles;
        $this->appId = $appId;
    }

    public static function createFromPayload($username, array $payload)
    {
        return new self(
            $payload['id'],
            $username,
            $payload['roles'],
            $payload['appId'],
        );
    }

    public function getEmail(): string
    {
        return $this->email;
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

    public function eraseCredentials(): void
    {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getAppId(): int
    {
        return $this->appId;
    }
}
