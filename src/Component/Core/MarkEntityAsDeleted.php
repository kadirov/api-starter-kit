<?php

declare(strict_types=1);

namespace App\Component\Core;

use App\Entity\Interfaces\IsDeletedSettableInterface;

class MarkEntityAsDeleted extends AbstractManager
{
    public function mark(IsDeletedSettableInterface $entity, bool $needToFlush = false): void
    {
        $entity->setIsDeleted(true);
        $this->save($entity, $needToFlush);
    }
}
