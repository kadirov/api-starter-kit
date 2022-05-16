<?php

declare(strict_types=1);

namespace App\Controller;

use App\Component\User\Dtos\UserDto;
use App\Component\User\Exceptions\AuthException;
use App\Component\User\TokensCreator;
use App\Controller\Base\AbstractController;
use App\Controller\Base\Constants\ResponseFormat;
use App\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTEncodeFailureException;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Class UserAuthAction
 *
 * @method UserDto getDtoFromRequest(Request $request, string $dtoClass)
 * @package App\Controller
 */
class UserAuthAction extends AbstractController
{
    public function __invoke(
        Request $request,
        UserRepository $userRepository,
        UserPasswordHasherInterface $passwordEncoder,
        JWTTokenManagerInterface $tokenManager,
        JWTEncoderInterface $tokenEncoder,
        TokensCreator $tokensCreator
    ): Response {
        $userDto = $this->getDtoFromRequest($request, UserDto::class);
        $user = $userRepository->findOneByEmail($userDto->getEmail());

        if ($user === null) {
            $this->throwInvalidCredentials();
        }

        if (!$passwordEncoder->isPasswordValid($user, $userDto->getPassword())) {
            $this->throwInvalidCredentials();
        }

        return $this->responseNormalized($tokensCreator->create($user), Response::HTTP_OK, ResponseFormat::JSON);
    }

    /**
     * @throws AuthException
     */
    private function throwInvalidCredentials(): void
    {
        throw new AuthException('Invalid credentials');
    }
}
