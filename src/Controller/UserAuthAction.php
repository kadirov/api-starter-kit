<?php

declare(strict_types=1);

namespace App\Controller;

use App\Component\User\Exceptions\AuthException;
use App\Component\User\TokensCreator;
use App\Controller\Base\AbstractController;
use App\Entity\User;
use App\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTEncodeFailureException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Class UserAuthAction
 *
 * @package App\Controller
 */
class UserAuthAction extends AbstractController
{
    /**
     * @throws JWTEncodeFailureException
     */
    public function __invoke(
        User $data,
        UserRepository $userRepository,
        UserPasswordHasherInterface $passwordEncoder,
        TokensCreator $tokensCreator
    ): Response {
        $user = $userRepository->findOneByEmail($data->getEmail());

        if ($user === null) {
            $this->throwInvalidCredentials();
        }

        if (!$passwordEncoder->isPasswordValid($user, $data->getPassword())) {
            $this->throwInvalidCredentials();
        }

        return $this->responseNormalized($tokensCreator->create($user));
    }

    /**
     * @throws AuthException
     */
    private function throwInvalidCredentials(): void
    {
        throw new AuthException('Invalid credentials');
    }
}
