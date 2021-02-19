<?php

declare(strict_types=1);

namespace App\Component\User;

use App\Component\User\Dtos\TokensDto;
use App\Entity\User;
use DateInterval;
use DateTime;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTEncodeFailureException;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class TokensCreator
{
    private JWTEncoderInterface $tokenEncoder;
    private JWTTokenManagerInterface $tokenManager;

    public function __construct(JWTEncoderInterface $tokenEncoder, JWTTokenManagerInterface $tokenManager)
    {
        $this->tokenEncoder = $tokenEncoder;
        $this->tokenManager = $tokenManager;
    }

    /**
     * @param User $user
     * @return TokensDto
     * @throws JWTEncodeFailureException
     */
    public function create(User $user): TokensDto
    {
        return new TokensDto($this->generateAccessToken($user), $this->generateRefreshToken($user->getId()));
    }

    /**
     * @param int $userId
     * @return string
     * @throws JWTEncodeFailureException
     */
    private function generateRefreshToken(int $userId): string
    {
        return $this->tokenEncoder->encode(
            [
                'id'  => $userId,
                'exp' => (new DateTime())->add(new DateInterval('P6M'))->getTimestamp(),
            ]
        );
    }

    private function generateAccessToken(UserInterface $user): string
    {
        $this->tokenManager->setUserIdentityField('id');
        return $this->tokenManager->create($user);
    }
}
