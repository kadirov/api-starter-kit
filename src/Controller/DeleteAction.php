<?php

declare(strict_types=1);

namespace App\Controller;

use App\Component\Core\MarkEntityAsDeleted;
use App\Controller\Base\AbstractController;
use App\Entity\Interfaces\DeletedAtSettableInterface;
use App\Entity\Interfaces\DeletedBySettableInterface;
use Symfony\Component\HttpFoundation\Response;

class DeleteAction extends AbstractController
{
    public function __invoke(
        DeletedAtSettableInterface|DeletedBySettableInterface $data,
        MarkEntityAsDeleted $markEntityAsDeleted
    ): Response {
        $markEntityAsDeleted->mark($data, true);
        return $this->responseEmpty();
    }
}
