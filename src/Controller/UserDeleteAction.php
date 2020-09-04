<?php declare(strict_types=1);

namespace App\Controller;

use App\Component\Core\MarkEntityAsDeleted;
use App\Component\User\UserManager;
use App\Controller\Base\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class UserDeleteAction extends AbstractController
{
    public function __invoke(
        UserManager $manager,
        MarkEntityAsDeleted $markEntityAsDeleted
    ): Response {
        $markEntityAsDeleted->mark($this->getUser(), $manager);
        return $this->responseEmpty();
    }
}
