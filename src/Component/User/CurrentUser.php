<?php

declare(strict_types=1);

namespace App\Component\User;

use App\Component\User\Dtos\JwtUserDto;
use App\Component\User\Exceptions\AuthException;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class CurrentUser
{
    private TokenStorageInterface $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function getUser(): User
    {
        $user = $this->getToken()->getUser();

        if (!$user instanceof User) {
            throw new AuthException('User is not found');
        }

        return $user;
    }

    public function getJwtUser(): JwtUserDto
    {
        $user = $this->getToken()->getUser();

        if (!$user instanceof JwtUserDto) {
            throw new AuthException('User is not found');
        }

        return $user;
    }

    public function isAuthed(): bool
    {
        return $this->tokenStorage->getToken() !== null;
    }

    private function getToken(): TokenInterface
    {
        $token = $this->tokenStorage->getToken();

        if ($token === null) {
            throw new AuthException('You should be authorized');
        }

        return $token;
    }
}
