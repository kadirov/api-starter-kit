<?php declare(strict_types=1);

namespace App\Controller;

use App\Component\User\Dtos\UserEmailDto;
use App\Controller\Base\AbstractController;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class CheckEmailController
 *
 * @package App\Controller
 */
class UserIsUniqueEmailAction extends AbstractController
{
    /**
     * @param Request        $request
     * @param UserRepository $userRepository
     * @return Response
     */
    public function __invoke(
        Request $request,
        UserRepository $userRepository
    ) {
        /**
         * @var UserEmailDto $checkEmailDto
         */
        $checkEmailDto = $this->getDtoFromRequest($request, UserEmailDto::class);
        $this->validate($checkEmailDto);

        return $this->responseNormalized(
            ['isUnique' => $userRepository->findOneByEmail($checkEmailDto->getEmail()) === null]
        );
    }
}
