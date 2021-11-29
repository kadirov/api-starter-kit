<?php declare(strict_types=1);

namespace App\Entity\Interfaces;

interface IsDeletedSettableInterface
{
    // todo rename to deletedAt
    public function setIsDeleted(bool $isDeleted): self;
}
