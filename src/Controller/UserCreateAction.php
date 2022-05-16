<?php

declare(strict_types=1);

namespace App\Controller;

use App\Component\User\Dtos\UserDto;
use App\Component\User\Exceptions\UserFactoryException;
use App\Component\User\UserMaker;
use App\Controller\Base\AbstractController;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Class CreateUserController
 *
 * @method UserDto getDtoFromRequest(Request $request, string $dtoClass)
 *
 * @package App\Controller
 */
class UserCreateAction extends AbstractController
{
    public function __invoke(
        Request $request,
        UserMaker $userMaker
    ): User {
        $userDto = $this->getDtoFromRequest($request, UserDto::class);
        $this->validate($userDto);

        try {
            return $userMaker->make($userDto);
        } catch (UserFactoryException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
    }
}
