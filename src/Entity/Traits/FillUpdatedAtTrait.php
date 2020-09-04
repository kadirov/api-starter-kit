<?php declare(strict_types=1);

namespace App\Entity\Traits;

use App\Entity\Interfaces\UpdatedAtSettableInterface;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * Trait FillCreatedAtTrait
 *
 * @mixin UpdatedAtSettableInterface
 * @package App\Entity\Traits
 */
trait FillUpdatedAtTrait
{
    /**
     * @ORM\PreUpdate()
     */
    public function fillUpdatedAt(): void
    {
        $this->setUpdatedAt(new DateTime());
    }
}
