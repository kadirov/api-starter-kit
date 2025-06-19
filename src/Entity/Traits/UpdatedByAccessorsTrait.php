<?php

namespace App\Entity\Traits;

use Symfony\Component\Security\Core\User\UserInterface;

trait UpdatedByAccessorsTrait
{
    public function getUpdatedBy(): ?UserInterface
    {
        return $this->updatedBy;
    }

    public function setUpdatedBy(UserInterface $user): static
    {
        $this->updatedBy = $user;

        return $this;
    }

}
