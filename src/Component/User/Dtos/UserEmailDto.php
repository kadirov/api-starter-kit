<?php declare(strict_types=1);

namespace App\Component\User\Dtos;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class CheckEmailDto
 *
 * @package App\Components\User\Dtos
 */
class UserEmailDto
{
    /**
     * @Assert\Email()
     */
    private string $email;

    /**
     * UserEmailDto constructor.
     *
     * @param string $email
     */
    public function __construct(string $email)
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }
}
