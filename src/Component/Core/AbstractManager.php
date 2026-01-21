<?php

declare(strict_types=1);

namespace App\Component\Core;

use App\Component\User\CurrentUser;
use App\Entity\Interfaces\CreatedAtSettableInterface;
use App\Entity\Interfaces\CreatedBySettableInterface;
use App\Entity\Interfaces\UpdatedAtSettableInterface;
use App\Entity\Interfaces\UpdatedBySettableInterface;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

abstract class AbstractManager
{
    private EntityManagerInterface $entityManager;
    private CurrentUser $currentUser;

    public function __construct(EntityManagerInterface $entityManager, CurrentUser $currentUser)
    {
        $this->entityManager = $entityManager;
        $this->currentUser = $currentUser;
    }

    public function save(object $entity, bool $needToFlush = false): void
    {
        $this->updateAuditFields($entity);
        $this->getEntityManager()->persist($entity);

        if ($needToFlush) {
            $this->entityManager->flush();
        }
    }

    protected function getEntityManager(): EntityManagerInterface
    {
        return $this->entityManager;
    }

    private function updateAuditFields(object $entity): void
    {
        $isNew = $entity->getId() === null;

        if ($isNew) {
            $this->setCreatedFields($entity);
        } else {
            $this->setUpdatedFields($entity);
        }
    }

    private function setCreatedFields(object $entity): void
    {
        if ($entity instanceof CreatedAtSettableInterface) {
            $entity->setCreatedAt(new DateTime());
        }

        if ($entity instanceof CreatedBySettableInterface && $this->currentUser->isAuthed()) {
            $entity->setCreatedBy($this->currentUser->getUser());
        }
    }

    private function setUpdatedFields(object $entity): void
    {
        if ($entity instanceof UpdatedAtSettableInterface) {
            $entity->setUpdatedAt(new DateTime());
        }

        if ($entity instanceof UpdatedBySettableInterface && $this->currentUser->isAuthed()) {
            $entity->setUpdatedBy($this->currentUser->getUser());
        }
    }
}
