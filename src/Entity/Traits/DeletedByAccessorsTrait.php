<?php

namespace App\Entity\Traits;

use DateTimeInterface;
use Symfony\Component\Security\Core\User\UserInterface;

trait DeletedByAccessorsTrait
{
    public function getDeletedBy(): ?UserInterface
    {
        return $this->deletedBy;
    }

    public function setDeletedBy(UserInterface $user): static
    {
        $this->deletedBy = $user;

        return $this;
    }
}
