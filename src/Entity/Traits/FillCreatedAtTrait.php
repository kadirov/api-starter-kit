<?php declare(strict_types=1);

namespace App\Entity\Traits;

use App\Entity\Interfaces\CreatedAtSettableInterface;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * Trait FillCreatedAtTrait
 *
 * @mixin CreatedAtSettableInterface
 * @package App\Entity\Traits
 */
trait FillCreatedAtTrait
{
    /**
     * @ORM\PrePersist()
     */
    public function fillCreatedAt(): void
    {
        $this->setCreatedAt(new DateTime());
    }
}
