<?php declare(strict_types=1);

namespace App\Controller;

use App\Component\Core\MarkEntityAsDeleted;
use App\Controller\Base\AbstractController;
use App\Entity\Interfaces\IsDeletedSettableInterface;

class DeleteAction extends AbstractController
{
    public function __invoke(IsDeletedSettableInterface $data, MarkEntityAsDeleted $markEntityAsDeleted)
    {
        $markEntityAsDeleted->mark($data);
        return $this->responseEmpty();
    }
}
