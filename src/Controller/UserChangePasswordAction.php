<?php declare(strict_types=1);

namespace App\Controller;

use App\Component\User\Dtos\UserPasswordDto;
use App\Component\User\UserManager;
use App\Controller\Base\AbstractController;
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
        UserManager $userManager
    ): Response {
        /** @var UserPasswordDto $userDto */
        $userDto = $this->getDtoFromRequest($request, UserPasswordDto::class);
        $this->validate($userDto);

        $userManager->hashPassword($this->getUser(), $userDto->getPassword());
        $userManager->save($this->getUser(), true);

        return $this->responseNormalized($this->getUser());
    }
}
