<?php

declare(strict_types=1);

namespace App\Controller\Subscribers;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Controller\Base\AbstractController;
use App\Entity\Interfaces\CreatedAtSettableInterface;
use App\Entity\Interfaces\UpdatedAtSettableInterface;
use App\Entity\Interfaces\UserIdSettableInterface;
use App\Entity\Interfaces\UserSettableInterface;
use DateTime;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class WriteSubscriber extends AbstractController implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => ['insertSubscriber', EventPriorities::PRE_WRITE],
        ];
    }

    public function insertSubscriber(ViewEvent $event): void
    {
        if ($this->isIgnoredUrl($event)) {
            return;
        }

        $model = $event->getControllerResult();

        switch ($event->getRequest()->getMethod()) {
            case Request::METHOD_POST:
                $this->persist($model);
                break;

            case Request::METHOD_PUT:
                $this->update($model);
                break;
        }
    }

    private function persist(mixed $model): void
    {
        if ($model instanceof UserSettableInterface) {
            $model->setUser($this->getUser());
        }

        if ($model instanceof UserIdSettableInterface) {
            $model->setUserId($this->getJwtUser()->getId());
        }

        if ($model instanceof CreatedAtSettableInterface) {
            $model->setCreatedAt(new DateTime());
        }
    }

    private function update(object $model): void
    {
        if ($model instanceof UpdatedAtSettableInterface) {
            $model->setUpdatedAt(new DateTime());
        }
    }

    private function isIgnoredUrl(ViewEvent $event): bool
    {
        switch ($event->getRequest()->getRequestUri()) {
            case '/api/some_ignored_url':
            case '/api/another_ignored_url':
                return true;

            default:
                return false;
        }
    }
}
