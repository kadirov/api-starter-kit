<?php declare(strict_types=1);

namespace App\Component\User;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException;

class CurrentUser
{
    private TokenStorageInterface $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function get(): User
    {
        $token = $this->tokenStorage->getToken();

        if ($token === null) {
            throw new AuthenticationCredentialsNotFoundException('You should be authorized');
        }

        $user = $token->getUser();

        if (!$user instanceof User) {
            throw new AuthenticationCredentialsNotFoundException('User is not found');
        }

        return $user;
    }
}
