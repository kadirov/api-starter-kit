<?php declare(strict_types=1);

namespace App\Component\User;

use App\Component\User\Dtos\UserDto;
use App\Component\User\Exceptions\UserFactoryException;
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

    /**
     * @param UserDto $userDto
     * @return User
     * @throws UserFactoryException
     */
    public function make(UserDto $userDto): User
    {
        $user = $this->userFactory->create($userDto);
        $this->userManager->save($user, true);

        return $user;
    }
}
