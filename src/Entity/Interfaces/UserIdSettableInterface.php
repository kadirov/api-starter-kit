<?php declare(strict_types=1);

namespace App\Entity\Interfaces;

interface UserIdSettableInterface
{
    // todo rename to createdBy or duplicate it
    // post->createdBy
    // person->user
    public function setUserId(int $userId);
}
