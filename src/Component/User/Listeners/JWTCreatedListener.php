<?php declare(strict_types=1);

namespace App\Component\User\Listeners;

use App\Component\User\Exceptions\AuthException;
use App\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Symfony\Component\HttpFoundation\RequestStack;

class JWTCreatedListener
{
    private UserRepository $userRepository;

    public function __construct(RequestStack $requestStack, UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @param JWTCreatedEvent $event
     *
     * @return void
     */
    public function onJWTCreated(JWTCreatedEvent $event): void
    {
        $payload = $event->getData();
        $payload['id'] = $payload['username'];

        $user = $this->userRepository->find($payload['id']);

        if ($user === null) {
            throw new AuthException('User is not found');
        }

        $payload['username'] = $user->getEmail();

        /** uncomment if you use microservices */
        // $payload['appId'] = $user->getApp()->getId();

        $event->setData($payload);

        $header = $event->getHeader();
        $header['cty'] = 'JWT';

        $event->setHeader($header);
    }
}
