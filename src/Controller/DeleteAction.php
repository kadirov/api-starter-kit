<?php declare(strict_types=1);

namespace App\Controller;

use App\Component\Core\Interfaces\IsDeletedInterface;
use App\Component\Core\MarkEntityAsDeleted;
use App\Controller\Base\AbstractController;

class DeleteAction extends AbstractController
{
    public function __invoke(IsDeletedInterface $data, MarkEntityAsDeleted $markEntityAsDeleted)
    {
        $markEntityAsDeleted->mark($data);
        return $this->responseEmpty();
    }
}
