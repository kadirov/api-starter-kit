<?php

declare(strict_types=1);

namespace App\Controller;

use App\Component\User\Dtos\RefreshTokenDto;
use App\Component\User\Dtos\RefreshTokenRequestDto;
use App\Component\User\Exceptions\AuthException;
use App\Component\User\TokensCreator;
use App\Controller\Base\AbstractController;
use App\Controller\Base\Constants\ResponseFormat;
use App\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTEncodeFailureException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/**
 * Class UserAuthAction
 *
 * @method RefreshTokenRequestDto getDtoFromRequest(Request $request, string $dtoClass)
 * @package App\Controller
 */
class UserAuthByRefreshTokenAction extends AbstractController
{
    /**
     * @param Request                      $request
     * @param UserRepository               $userRepository
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param JWTEncoderInterface          $tokenEncoder
     * @param TokensCreator                $tokensCreator
     * @param DenormalizerInterface        $denormalizer
     * @return Response
     * @throws ExceptionInterface
     * @throws JWTDecodeFailureException
     * @throws JWTEncodeFailureException
     */
    public function __invoke(
        Request $request,
        UserRepository $userRepository,
        UserPasswordEncoderInterface $passwordEncoder,
        JWTEncoderInterface $tokenEncoder,
        TokensCreator $tokensCreator,
        DenormalizerInterface $denormalizer
    ): Response {
        $requestDto = $this->getDtoFromRequest($request, RefreshTokenRequestDto::class);
        $refreshTokenDto = $this->arrayToDto($denormalizer, $tokenEncoder->decode($requestDto->getRefreshToken()));

        $user = $userRepository->find($refreshTokenDto->getId());

        if ($user === null) {
            $this->throwInvalidCredentials();
        }

        if ($user->getUpdatedAt() !== null && $user->getUpdatedAt()->getTimestamp() > $refreshTokenDto->getIat()) {
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

    /**
     * @param DenormalizerInterface $denormalizer
     * @param array                 $data
     * @return RefreshTokenDto|object
     * @throws ExceptionInterface
     */
    private function arrayToDto(DenormalizerInterface $denormalizer, array $data): RefreshTokenDto
    {
        return $denormalizer->denormalize($data, RefreshTokenDto::class);
    }
}
