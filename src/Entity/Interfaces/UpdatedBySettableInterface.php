<?php declare(strict_types=1);

namespace App\Entity\Interfaces;

use Symfony\Component\Security\Core\User\UserInterface;

interface UpdatedBySettableInterface
{
    public function setUpdatedBy(UserInterface $user);
}
