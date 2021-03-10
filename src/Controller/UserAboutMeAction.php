<?php

declare(strict_types=1);

namespace App\Controller;

use App\Controller\Base\AbstractController;
use App\Entity\User;

class UserAboutMeAction extends AbstractController
{
    public function __invoke(): User
    {
        return $this->getUser();
    }
}
