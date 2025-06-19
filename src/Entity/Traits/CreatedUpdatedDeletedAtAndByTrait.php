<?php

namespace App\Entity\Traits;

trait CreatedUpdatedDeletedAtAndByTrait
{
    use CreatedAtAndByAccessorsTrait;
    use UpdatedAtAndByAccessorsTrait;
    use DeletedAtAndByAccessorsTrait;
}
