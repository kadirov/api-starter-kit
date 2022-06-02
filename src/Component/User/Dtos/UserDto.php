<?php
declare(strict_types=1);

namespace App\Component\User\Dtos;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class AuthenticationTokenDto
 *
 * @package App\Component\User\Dtos
 * @deprecated
 * @todo
 */
class UserDto
{
    /**
     * @Assert\Email()
     * @Assert\NotBlank()
     */
    private string $email;

    /**
     * @Assert\Length(min="6")
     */
    private string $password;

    public function __construct(string $email, string $password)
    {
        $this->email = $email;
        $this->password = $password;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }
}
