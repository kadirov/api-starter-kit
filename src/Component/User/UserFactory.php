<?php declare(strict_types=1);

namespace App\Component\User;

use App\Component\User\Dtos\UserDto;
use App\Component\User\Exceptions\UserFactoryException;
use App\Entity\User;
use DateTime;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserFactory
{
    private UserManager $userManager;
    private ValidatorInterface $validator;

    public function __construct(UserManager $userManager, ValidatorInterface $validator)
    {
        $this->userManager = $userManager;
        $this->validator = $validator;
    }

    /**
     * @param UserDto $userDto
     * @return User
     * @throws UserFactoryException
     */
    public function create(UserDto $userDto): User
    {
        $user = new User();

        $user->setEmail($userDto->getEmail());
        $user->setCreatedAt(new DateTime());

        $this->userManager->hashPassword($user, $userDto->getPassword());

        $errors = $this->validator->validate($user);

        if (count($errors) > 0) {
            throw new UserFactoryException((string)$errors);
        }

        return $user;
    }
}
