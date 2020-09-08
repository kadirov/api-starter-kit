<?php declare(strict_types=1);

namespace App\Component\User;

use App\Component\User\Exceptions\UserFactoryException;
use App\Entity\User;
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
     * @param string $email
     * @param string $plainPassword
     * @return User
     * @throws UserFactoryException
     */
    public function create(string $email, string $plainPassword): User
    {
        $user = new User();

        $user->setEmail($email);
        $this->userManager->hashPassword($user, $plainPassword);

        $errors = $this->validator->validate($user);

        if (count($errors) > 0) {
            throw new UserFactoryException((string)$errors);
        }

        return $user;
    }
}
