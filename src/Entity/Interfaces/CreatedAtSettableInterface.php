<?php declare(strict_types=1);

namespace App\Entity\Interfaces;

use DateTimeInterface;

interface CreatedAtSettableInterface
{
    public function setCreatedAt(DateTimeInterface $dateTime);
}
