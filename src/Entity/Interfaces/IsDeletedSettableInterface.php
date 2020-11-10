<?php declare(strict_types=1);

namespace App\Entity\Interfaces;

interface IsDeletedSettableInterface
{
    public function setIsDeleted(bool $isDeleted): self;
}
