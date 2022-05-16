<?php

declare(strict_types=1);

namespace App\Controller;

use App\Component\User\Dtos\UserEmailDto;
use App\Controller\Base\AbstractController;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class CheckEmailController
 *
 * @method UserEmailDto getDtoFromRequest(Request $request, string $dtoClass)
 *
 * @package App\Controller
 */
class UserIsUniqueEmailAction extends AbstractController
{
    public function __invoke(
        Request $request,
        UserRepository $userRepository
    ): Response {
        $checkEmailDto = $this->getDtoFromRequest($request, UserEmailDto::class);
        $this->validate($checkEmailDto);

        $user = $userRepository->findOneByEmail($checkEmailDto->getEmail());

        return $this->responseNormalized(
            ['isUnique' => $user === null]
        );
    }
}
