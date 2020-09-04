<?php declare(strict_types=1);

namespace App\Component\Core;

use App\Component\Core\Interfaces\IsDeletedInterface;

class MarkEntityAsDeleted
{
    public function mark(IsDeletedInterface $entity, AbstractManager $manager): void
    {
        $entity->setIsDeleted(true);
        $manager->save($entity, true);
    }
}
