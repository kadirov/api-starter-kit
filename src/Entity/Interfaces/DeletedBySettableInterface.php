<?php
declare(strict_types=1);

namespace App\Entity\Interfaces;

use Symfony\Component\Security\Core\User\UserInterface;

interface DeletedBySettableInterface
{
    public function setDeletedBy(UserInterface $user): self;
}
