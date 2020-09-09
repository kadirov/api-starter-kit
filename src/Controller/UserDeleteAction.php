<?php declare(strict_types=1);

namespace App\Controller;

use App\Component\Core\MarkEntityAsDeleted;
use App\Component\User\UserManager;
use App\Controller\Base\AbstractController;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;

class UserDeleteAction extends AbstractController
{
    public function __invoke(
        UserManager $manager,
        MarkEntityAsDeleted $markEntityAsDeleted,
        UserRepository $repository,
        int $id
    ): Response {
        /** @var User $user */
        $user = $this->getEntityOrError($repository, $id);

        $markEntityAsDeleted->mark($user, $manager);
        return $this->responseEmpty();
    }
}
