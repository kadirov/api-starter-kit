<?php declare(strict_types=1);

namespace App\Controller;

use App\Component\User\Dtos\UserPasswordDto;
use App\Component\User\UserManager;
use App\Controller\Base\AbstractController;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class CreateUserController
 *
 * @package App\Controller
 */
class UserChangePasswordAction extends AbstractController
{
    public function __invoke(
        Request $request,
        UserManager $userManager,
        UserRepository $repository,
        int $id
    ): Response {
        /** @var User $user */
        $user = $this->getEntityOrError($repository, $id);

        /** @var UserPasswordDto $userDto */
        $userDto = $this->getDtoFromRequest($request, UserPasswordDto::class);
        $this->validate($userDto);

        $userManager->hashPassword($user, $userDto->getPassword());
        $userManager->save($user, true);

        return $this->responseNormalized($user);
    }
}
