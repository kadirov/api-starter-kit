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
     * @Assert\Length(min="6")
     */
    private string $password;

    /**
     * UserDto constructor.
     *
     * @param string $email
     * @param string $password
     */
    public function __construct(string $email, string $password)
    {
        $this->email = $email;
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }
}
