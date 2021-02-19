<?php

declare(strict_types=1);

namespace App\Controller;

use App\Component\User\Dtos\UserDto;
use App\Component\User\Exceptions\AuthException;
use App\Component\User\TokensCreator;
use App\Controller\Base\AbstractController;
use App\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTEncodeFailureException;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class UserAuthAction
 *
 * @method UserDto getDtoFromRequest(Request $request, string $dtoClass)
 * @package App\Controller
 */
class UserAuthAction extends AbstractController
{
    /**
     * @param Request                      $request
     * @param UserRepository               $userRepository
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param JWTTokenManagerInterface     $tokenManager
     * @param JWTEncoderInterface          $tokenEncoder
     * @param TokensCreator                $tokensCreator
     * @return Response
     * @throws JWTEncodeFailureException
     */
    public function __invoke(
        Request $request,
        UserRepository $userRepository,
        UserPasswordEncoderInterface $passwordEncoder,
        JWTTokenManagerInterface $tokenManager,
        JWTEncoderInterface $tokenEncoder,
        TokensCreator $tokensCreator
    ): Response {
        $userDto = $this->getDtoFromRequest($request, UserDto::class);
        $user = $userRepository->findOneByEmail($userDto->getEmail());

        /**
         * or uncomment if you use microservices
         * $user = $userRepository->findOneByEmailAndApp($userDto->getEmail(), $userDto->getApp());
         */

        if ($user === null) {
            $this->throwInvalidCredentials();
        }

        if (!$passwordEncoder->isPasswordValid($user, $userDto->getPassword())) {
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
