<?php declare(strict_types=1);

namespace App\Component\User;

use App\Component\User\Exceptions\AuthException;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

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
            throw new AuthException('You should be authorized');
        }

        $user = $token->getUser();

        // if (!$user instanceof JwtUserDto) {
        if (!$user instanceof User) {
            throw new AuthException('User is not found');
        }

        return $user;
    }
}
