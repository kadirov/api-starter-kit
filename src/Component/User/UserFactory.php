<?php declare(strict_types=1);

namespace App\Component\User;

use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFactory
{
    private UserPasswordEncoderInterface $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function create(string $email, string $plainPassword): User
    {
        $user = new User();

        $user->setEmail($email);
        $user->setPassword(
            $this->encoder->encodePassword($user, $plainPassword)
        );

        return $user;
    }
}
