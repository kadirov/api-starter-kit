<?php declare(strict_types=1);

namespace App\Component\User\Dtos;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class AuthenticationTokenDto
 *
 * @package App\Component\User\Dtos
 */
class UserDto
{
    /**
     * @Assert\Email()
     * @Assert\NotBlank()
     */
    private string $email;

    /**
     * @Assert\NotBlank()
     */
    private string $password;

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }
}
