<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Component\User\Dtos\RefreshTokenRequestDto;
use App\Controller\DeleteAction;
use App\Controller\UserAboutMeAction;
use App\Controller\UserAuthAction;
use App\Controller\UserAuthByRefreshTokenAction;
use App\Controller\UserChangePasswordAction;
use App\Controller\UserCreateAction;
use App\Controller\UserIsUniqueEmailAction;
use App\Entity\Interfaces\CreatedAtSettableInterface;
use App\Entity\Interfaces\IsDeletedSettableInterface;
use App\Entity\Interfaces\UpdatedAtSettableInterface;
use App\Repository\UserRepository;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource(
 *      normalizationContext = {"groups" = {"users:read"}},
 *      denormalizationContext = {"groups" = {"users:write"}},
 *      collectionOperations = {
 *          "get" = {
 *              "security" = "is_granted('ROLE_ADMIN')",
 *          },
 *          "post" = {
 *              "controller" = UserCreateAction::class,
 *              "normalization_context" = { "groups" = {"user:read", "users:read"}},
 *          },
 *          "aboutMe" = {
 *              "controller" = UserAboutMeAction::class,
 *              "method" = "get",
 *              "path" = "/users/about_me",
 *              "normalization_context" = { "groups" = {"user:read", "users:read"}},
 *          },
 *          "auth" = {
 *              "controller" = UserAuthAction::class,
 *              "method" = "post",
 *              "path" = "/users/auth",
 *          },
 *          "authByRefreshToken" = {
 *              "controller" = UserAuthByRefreshTokenAction::class,
 *              "method" = "post",
 *              "path" = "/users/auth/refreshToken",
 *              "input" = RefreshTokenRequestDto::class,
 *          },
 *          "isUniqueEmail" = {
 *              "controller" = UserIsUniqueEmailAction::class,
 *              "method" = "post",
 *              "path" = "users/is_unique_email",
 *              "normalization_context" = { "groups" = {"user:read", "users:read"}},
 *              "denormalization_context" = { "groups" = {"users:isUniqueEmail:write"}},
 *          },
 *      },
 *      itemOperations = {
 *          "changePassword" = {
 *              "security" = "object == user || is_granted('ROLE_ADMIN')",
 *              "controller" = UserChangePasswordAction::class,
 *              "normalization_context" = { "groups" = {"user:read", "users:read"}},
 *              "denormalization_context" = { "groups" = {"users:changePassword:write"}},
 *              "method" = "put",
 *              "path" = "users/{id}/password",
 *          },
 *          "delete" = {
 *              "security" = "object == user || is_granted('ROLE_ADMIN')",
 *              "controller" = DeleteAction::class,
 *          },
 *          "get" = {
 *              "security" = "object == user || is_granted('ROLE_ADMIN')",
 *              "normalization_context" = { "groups" = {"user:read", "users:read"}},
 *          },
 *          "put" = {
 *              "security" = "object == user || is_granted('ROLE_ADMIN')",
 *              "normalization_context" = { "groups" = {"user:read", "users:read"}},
 *              "denormalization_context" = { "groups" = {"users:put:write"}},
 *          },
 *      },
 * )
 * @ApiFilter(OrderFilter::class, properties={"id", "createdAt", "updatedAt", "email"})
 * @ApiFilter(SearchFilter::class, properties={"id": "exact", "email": "partial"})
 *
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @see OrderFilter
 * @see SearchFilter
 * @see UserCreateAction
 * @see UserAboutMeAction
 * @see UserIsUniqueEmailAction
 * @see UserChangePasswordAction
 * @see UserAuthAction
 * @see DeleteAction
 * @see UserAuthByRefreshTokenAction
 * @see RefreshTokenRequestDto
 */
class User implements
    UserInterface,
    UpdatedAtSettableInterface,
    CreatedAtSettableInterface,
    IsDeletedSettableInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"users:read"})
     */
    private $id;

    /**
     * @Assert\Email()
     * @ORM\Column(type="string", length=255)
     * @Groups({"users:read", "users:write", "users:put:write", "users:isUniqueEmail:write"})
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"users:write", "users:changePassword:write"})
     */
    private $password;

    /**
     * @ORM\Column(type="array")
     * @Groups({"user:read"})
     */
    private $roles = [];

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"user:read"})
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups({"user:read"})
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

    public function addRole(string $role): self
    {
        if (!in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }

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

//    public function getApp(): ?App
//    {
//        return $this->app;
//    }
//
//    public function setApp(?App $app): self
//    {
//        $this->app = $app;
//
//        return $this;
//    }
}
