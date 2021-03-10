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

    public function __construct(string $email, string $password /*, App $app */)
    {
        $this->email = $email;
        $this->password = $password;
//        $this->app = $app;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

//    public function getApp(): App
//    {
//        return $this->app;
//    }
}
