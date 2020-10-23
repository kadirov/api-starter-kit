<?php declare(strict_types=1);

namespace App\Controller\Subscribers;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Controller\Base\AbstractController;
use App\Entity\Interfaces\AppIdSettableInterface;
use App\Entity\Interfaces\UserIdSettableInterface;
use App\Entity\Interfaces\UserSettableInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
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
        $model = $event->getControllerResult();
        $method = strtolower($event->getRequest()->getMethod());

        switch ($method) {
            case 'post':
                $this->persist($model);
                break;

            case 'put':
                $this->update($model);
                break;
        }
    }

    private function persist(object $model): void
    {
        if ($model instanceof UserSettableInterface) {
            $model->setUser($this->getUser());
        }

        if ($model instanceof UserIdSettableInterface) {
            $model->setUserId($this->getJwtUser()->getId());
        }

        if ($model instanceof AppIdSettableInterface) {
            $model->setAppId($this->getJwtUser()->getAppId());
        }
    }

    private function update(object $model): void
    {
    }
}
