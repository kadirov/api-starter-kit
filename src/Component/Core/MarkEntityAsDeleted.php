<?php declare(strict_types=1);

namespace App\Component\Core;

use App\Entity\Interfaces\IsDeletedSettableInterface;
use Doctrine\ORM\EntityManagerInterface;

class MarkEntityAsDeleted
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function mark(IsDeletedSettableInterface $entity): void
    {
        $entity->setIsDeleted(true);
        $this->em->persist($entity);
        $this->em->flush();
    }
}
