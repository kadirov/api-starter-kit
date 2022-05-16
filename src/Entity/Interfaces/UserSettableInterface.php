<?php declare(strict_types=1);

namespace App\Entity\Interfaces;

use Symfony\Component\Security\Core\User\UserInterface;

interface UserSettableInterface
{
    // todo rename to createdBy or duplicate it
    // post->createdBy
    // person->user
    public function setUser(UserInterface $user);
}
