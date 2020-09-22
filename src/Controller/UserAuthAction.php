<?php declare(strict_types=1);

namespace App\Controller;

use App\Component\User\Dtos\UserDto;
use App\Component\User\Exceptions\AuthException;
use App\Controller\Base\AbstractController;
use App\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserAuthAction extends AbstractController
{
    /**
     * @param Request                      $request
     * @param UserRepository               $userRepository
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param JWTTokenManagerInterface     $tokenManager
     * @return Response
     * @throws AuthException
     */
    public function __invoke(
        Request $request,
        UserRepository $userRepository,
        UserPasswordEncoderInterface $passwordEncoder,
        JWTTokenManagerInterface $tokenManager
    ): Response {
        /** @var UserDto $userDto */
        $userDto = $this->getDtoFromRequest($request, UserDto::class);
        $user = $userRepository->findOneByEmail($userDto->getEmail());

        if ($user === null) {
            $this->throwInvalidCredentials();
        }

        if (!$passwordEncoder->isPasswordValid($user, $userDto->getPassword())) {
            $this->throwInvalidCredentials();
        }

        $tokenManager->setUserIdentityField('id');

        return $this->responseNormalized(['token' => $tokenManager->create($user)]);
    }

    /**
     * @throws AuthException
     */
    private function throwInvalidCredentials(): void
    {
        throw new AuthException('Invalid credentials');
    }
}
