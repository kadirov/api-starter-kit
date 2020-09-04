<?php declare(strict_types=1);

namespace App\Controller;

use App\Component\User\Dtos\UserDto;
use App\Component\User\UserMaker;
use App\Controller\Base\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class CreateUserController
 *
 * @package App\Controller
 */
class UserCreateAction extends AbstractController
{
    public function __invoke(
        Request $request,
        UserMaker $userMaker
    ): Response {
        /** @var UserDto $userDto */
        $userDto = $this->getDtoFromRequest($request, UserDto::class);
        $this->validate($userDto);

        $user = $userMaker->make($userDto);

        return $this->responseNormalized($user);
    }
}
