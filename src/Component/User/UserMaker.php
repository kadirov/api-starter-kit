<?php declare(strict_types=1);

namespace App\Component\User;

use App\Component\User\Dtos\UserDto;
use App\Entity\User;

class UserMaker
{
    private UserFactory $userFactory;
    private UserManager $userManager;

    public function __construct(
        UserFactory $userFactory,
        UserManager $userManager
    ) {
        $this->userFactory = $userFactory;
        $this->userManager = $userManager;
    }

    public function make(UserDto $userDto): User
    {
        $user = $this->userFactory->create($userDto->getEmail(), $userDto->getPassword());
        $this->userManager->save($user, true);

        return $user;
    }
}
