<?php declare(strict_types=1);

namespace App\Entity\Interfaces;

use DateTimeInterface;

interface UpdatedAtSettableInterface
{
    public function setUpdatedAt(DateTimeInterface $updatedAt);
}
