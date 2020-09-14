<?php declare(strict_types=1);

namespace App\Component\User\Listeners;

use App\Component\User\CurrentUser;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;

class JWTCreatedListener
{
    private CurrentUser $user;

    public function __construct(CurrentUser $user)
    {
        $this->user = $user;
    }

    /**
     * @param JWTCreatedEvent $event
     *
     * @return void
     */
    public function onJWTCreated(JWTCreatedEvent $event): void
    {
        $payload = $event->getData();
        $payload['id'] = $this->user->get()->getId();
        $event->setData($payload);

        $header = $event->getHeader();
        $header['cty'] = 'JWT';
        $event->setHeader($header);
    }
}
