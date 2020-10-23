<?php declare(strict_types=1);

namespace App\Component\Core;

use App\Component\Core\Interfaces\IsDeletedInterface;
use Doctrine\ORM\EntityManagerInterface;

class MarkEntityAsDeleted
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function mark(IsDeletedInterface $entity): void
    {
        $entity->setIsDeleted(true);
        $this->em->persist($entity);
        $this->em->flush();
    }
}
