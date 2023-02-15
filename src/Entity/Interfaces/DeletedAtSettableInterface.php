<?php declare(strict_types=1);

namespace App\Entity\Interfaces;

use DateTimeInterface;

interface DeletedAtSettableInterface
{
    public function setDeletedAt(DateTimeInterface $deletedAt): self;
}
