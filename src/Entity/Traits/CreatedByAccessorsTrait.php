<?php

namespace App\Entity\Traits;

use Symfony\Component\Security\Core\User\UserInterface;

trait CreatedByAccessorsTrait
{
    public function getCreatedBy(): ?UserInterface
    {
        return $this->createdBy;
    }

    public function setCreatedBy(UserInterface $user): static
    {
        $this->createdBy = $user;

        return $this;
    }
}
