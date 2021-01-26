<?php declare(strict_types=1);

namespace App\Component\Core;

use App\Entity\Interfaces\UpdatedAtSettableInterface;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

abstract class AbstractManager
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    protected function getEntityManager(): EntityManagerInterface
    {
        return $this->entityManager;
    }

    public function save(object $entity, bool $needToFlush = false): void
    {
        if ($entity->getId() !== null && $entity instanceof UpdatedAtSettableInterface) {
            $entity->setUpdatedAt(new DateTime());
        }

        $this->getEntityManager()->persist($entity);

        if ($needToFlush) {
            $this->entityManager->flush();
        }
    }
}
