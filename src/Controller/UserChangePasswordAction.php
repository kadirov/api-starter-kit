<?php

declare(strict_types=1);

namespace App\Controller;

use App\Component\User\Dtos\UserPasswordDto;
use App\Component\User\UserManager;
use App\Controller\Base\AbstractController;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class CreateUserController
 *
 * @method User findEntityOrError(ServiceEntityRepository $repository, int $id)
 * @method UserPasswordDto getDtoFromRequest(Request $request, string $dtoClass)
 *
 * @package App\Controller
 */
class UserChangePasswordAction extends AbstractController
{
    public function __invoke(
        User $data,
        UserManager $userManager,
        UserRepository $repository,
        int $id
    ): User {
        $user = $this->findEntityOrError($repository, $id);
        $this->validate($data);

        $userManager->hashPassword($user, $data->getPassword());
        $userManager->save($user, true);

        return $user;
    }
}
