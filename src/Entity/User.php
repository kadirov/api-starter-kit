<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Component\Core\Interfaces\IsDeletedInterface;
use App\Controller\UserAboutMeAction;
use App\Controller\UserChangePasswordAction;
use App\Controller\UserCreateAction;
use App\Controller\UserDeleteAction;
use App\Controller\UserIsUniqueEmailAction;
use App\Entity\Interfaces\CreatedAtSettableInterface;
use App\Entity\Interfaces\UpdatedAtSettableInterface;
use App\Entity\Traits\FillCreatedAtTrait;
use App\Entity\Traits\FillUpdatedAtTrait;
use App\Repository\UserRepository;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource(
 *     normalizationContext = {"groups" = {"users:read"}},
 *     denormalizationContext = {"groups" = {"users:write"}},
 *     collectionOperations = {
 *          "aboutMe" = {
 *              "controller" = UserAboutMeAction::class,
 *              "method" = "get",
 *              "path" = "/users/about_me",
 *          },
 *          "auth" = {
 *              "method" = "post",
 *              "path" = "/authentication_token",
 *          },
 *          "get" = {
 *              "access_control" = "is_granted('ROLE_ADMIN')",
 *          },
 *          "isUniqueEmail" = {
 *              "controller" = UserIsUniqueEmailAction::class,
 *              "method" = "post",
 *              "path" = "users/is_unique_email",
 *          },
 *          "post" = {
 *              "controller" = UserCreateAction::class,
 *          },
 *     },
 *     itemOperations = {
 *          "changePassword" = {
 *              "access_control" = "object == user",
 *              "controller" = UserChangePasswordAction::class,
 *              "denormalization_context" = { "groups" = {"users:changePassword:write"}},
 *              "method" = "put",
 *              "path" = "users/{id}/password",
 *          },
 *          "delete" = {
 *              "access_control" = "object == user",
 *              "controller" = UserDeleteAction::class,
 *           },
 *          "get" = { "access_control" = "object == user" },
 *          "put" = {
 *              "access_control" = "object == user",
 *              "denormalization_context" = { "groups" = {"users:put:write"}},
 *          },
 *     },
 * )
 * @UniqueEntity("email", message="This email is already used")
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @see UserCreateAction
 * @see UserDeleteAction
 * @see UserAboutMeAction
 * @see UserIsUniqueEmailAction
 * @see UserChangePasswordAction
 */
class User implements UserInterface, UpdatedAtSettableInterface, CreatedAtSettableInterface, IsDeletedInterface
{
    use FillCreatedAtTrait;
    use FillUpdatedAtTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"users:read"})
     */
    private $id;

    /**
     * @Assert\Email()
     * @ORM\Column(type="string", length=255, unique=true)
     * @Groups({"users:read", "users:write", "users:put:write"})
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"users:write", "users:changePassword:write"})
     */
    private $password;

    /**
     * @ORM\Column(type="array")
     * @Groups({"users:read"})
     */
    private $roles = [];

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isDeleted = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getRoles(): ?array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getSalt()
    {
        // TODO: Implement getSalt() method.
    }

    public function getUsername()
    {
        return $this->getEmail();
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getIsDeleted(): ?bool
    {
        return $this->isDeleted;
    }

    public function setIsDeleted(bool $isDeleted): self
    {
        $this->isDeleted = $isDeleted;

        return $this;
    }
}
