<?php

declare(strict_types=1);

namespace App\Component\Core;

use App\Component\User\CurrentUser;
use App\Entity\Interfaces\DeletedAtSettableInterface;
use App\Entity\Interfaces\DeletedBySettableInterface;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

class MarkEntityAsDeleted extends AbstractManager
{
    public function __construct(EntityManagerInterface $entityManager, private CurrentUser $currentUser)
    {
        parent::__construct($entityManager);
    }

    public function mark(DeletedAtSettableInterface|DeletedBySettableInterface $entity, bool $needToFlush = false): void
    {
        if ($entity instanceof DeletedAtSettableInterface) {
            $entity->setDeletedAt(new DateTime());
        }

        if ($entity instanceof DeletedBySettableInterface) {
            $entity->setDeletedBy($this->currentUser->getUser());
        }

        $this->save($entity, $needToFlush);
    }
}
