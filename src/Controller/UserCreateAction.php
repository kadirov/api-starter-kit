<?php

declare(strict_types=1);

namespace App\Controller;

use App\Component\User\UserFactory;
use App\Component\User\UserManager;
use App\Controller\Base\AbstractController;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Class CreateUserController
 *
 * @package App\Controller
 */
class UserCreateAction extends AbstractController
{
    public function __invoke(
        User $data,
        UserFactory $userFactory,
        UserManager $userManager,
        UserRepository $userRepository
    ): User {
        $this->validate($data);

        if ($userRepository->findOneByEmail($data->getEmail())) {
            throw new BadRequestHttpException('Email already taken');
        }

        $user = $userFactory->create($data->getEmail(), $data->getPassword());
        $userManager->save($user, true);

        return $user;
    }
}
