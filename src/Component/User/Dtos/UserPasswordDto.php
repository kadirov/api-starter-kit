<?php declare(strict_types=1);

namespace App\Component\User\Dtos;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class CheckEmailDto
 *
 * @package App\Components\User\Dtos
 */
class UserPasswordDto
{
    /**
     * @Assert\Length(min="6")
     */
    private string $password;

    public function __construct(string $password)
    {
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }
}
