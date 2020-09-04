<?php declare(strict_types=1);

namespace App\Component\Core\Interfaces;

interface IsDeletedInterface
{
    public function setIsDeleted(bool $isDeleted): self;
}
