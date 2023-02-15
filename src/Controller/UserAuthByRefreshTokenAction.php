<?php

declare(strict_types=1);

namespace App\Controller;

use App\Component\User\Dtos\RefreshTokenDto;
use App\Component\User\Dtos\RefreshTokenRequestDto;
use App\Component\User\Dtos\TokensDto;
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
     * @throws JWTDecodeFailureException
     * @throws ExceptionInterface
     * @throws JWTEncodeFailureException
     */
    public function __invoke(
        Request $request,
        UserRepository $userRepository,
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

        return $this->responseNormalized($tokensCreator->create($user));
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
     * @param array $data
     * @return RefreshTokenDto
     * @throws ExceptionInterface
     */
    private function arrayToDto(DenormalizerInterface $denormalizer, array $data): RefreshTokenDto
    {
        return $denormalizer->denormalize($data, RefreshTokenDto::class);
    }
}
