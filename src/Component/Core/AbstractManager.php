<?php declare(strict_types=1);

namespace App\Component\Core;

use App\Component\Core\Interfaces\IsDeletedInterface;
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
        $this->getEntityManager()->persist($entity);

        if ($needToFlush) {
            $this->entityManager->flush();
        }
    }
}
