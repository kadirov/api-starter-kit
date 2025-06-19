<?php declare(strict_types=1);

namespace App\Entity\Interfaces;

use Symfony\Component\Security\Core\User\UserInterface;

interface CreatedBySettableInterface
{
    public function setCreatedBy(UserInterface $user): static;
}
