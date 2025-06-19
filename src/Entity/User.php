<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\OpenApi\Model\Operation;
use App\Component\User\Dtos\RefreshTokenRequestDto;
use App\Component\User\Dtos\TokensDto;
use App\Controller\DeleteAction;
use App\Controller\UserAboutMeAction;
use App\Controller\UserAuthAction;
use App\Controller\UserAuthByRefreshTokenAction;
use App\Controller\UserChangePasswordAction;
use App\Controller\UserCreateAction;
use App\Controller\UserIsUniqueEmailAction;
use App\Entity\Interfaces\CreatedAtSettableInterface;
use App\Entity\Interfaces\DeletedAtSettableInterface;
use App\Entity\Interfaces\DeletedBySettableInterface;
use App\Entity\Interfaces\UpdatedAtSettableInterface;
use App\Entity\Interfaces\UpdatedBySettableInterface;
use App\Entity\Traits\CreatedAtAccessorsTrait;
use App\Entity\Traits\DeletedAtAndByAccessorsTrait;
use App\Entity\Traits\UpdatedAtAndByAccessorsTrait;
use App\Repository\UserRepository;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new GetCollection(
            normalizationContext: ['groups' => ['users:read']],
            security: "is_granted('ROLE_ADMIN')",
        ),
        new Get(
            security: "object == user || is_granted('ROLE_ADMIN')",
        ),
        new Post(
            controller: UserCreateAction::class,
        ),
        new Patch(
            denormalizationContext: ['groups' => ['user:put:write']],
            security: "object == user || is_granted('ROLE_ADMIN')",
        ),
        new Delete(
            controller: DeleteAction::class,
            security: "object == user || is_granted('ROLE_ADMIN')",
        ),
        new Post(
            uriTemplate: 'users/about_me',
            controller: UserAboutMeAction::class,
            openapi: new Operation(
                summary: 'Shows info about the authenticated user'
            ),
            denormalizationContext: ['groups' => ['user:empty:body']],
            input: false,
            name: 'aboutMe',
        ),
        new Post(
            uriTemplate: 'users/auth',
            controller: UserAuthAction::class,
            openapi: new Operation(
                summary: 'Authorization'
            ),
            output: TokensDto::class,
            name: 'auth',
        ),
        new Post(
            uriTemplate: 'users/auth/refreshToken',
            controller: UserAuthByRefreshTokenAction::class,
            openapi: new Operation(
                summary: 'Authorization by refreshToken'
            ),
            input: RefreshTokenRequestDto::class,
            output: TokensDto::class,
            name: 'authByRefreshToken',
        ),
        new Post(
            uriTemplate: 'users/is_unique_email',
            controller: UserIsUniqueEmailAction::class,
            openapi: new Operation(
                summary: 'Checks email for uniqueness'
            ),
            denormalizationContext: ['groups' => ['user:isUniqueEmail:write']],
            name: 'isUniqueEmail',
        ),
        new Patch(
            uriTemplate: 'users/{id}/password',
            controller: UserChangePasswordAction::class,
            openapi: new Operation(
                summary: 'Changes password'
            ),
            denormalizationContext: ['groups' => ['user:changePassword:write']],
            security: "object == user || is_granted('ROLE_ADMIN')",
            name: 'changePassword',
        ),
    ],
    normalizationContext: ['groups' => ['user:read', 'users:read']],
    denormalizationContext: ['groups' => ['user:write']],
    extraProperties: [
        'standard_put' => true,
    ],
)]
#[ApiFilter(OrderFilter::class, properties: ['id', 'createdAt', 'updatedAt', 'email'])]
#[ApiFilter(SearchFilter::class, properties: ['id' => 'exact', 'email' => 'partial'])]
//#[UniqueEntity('email', message: 'This email is already used')]
#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements
    UserInterface,
    CreatedAtSettableInterface,
    UpdatedAtSettableInterface,
    UpdatedBySettableInterface,
    DeletedAtSettableInterface,
    DeletedBySettableInterface,
    PasswordAuthenticatedUserInterface
{
//    use CreatedUpdatedDeletedAtAndByTrait;
    use CreatedAtAccessorsTrait;
    use UpdatedAtAndByAccessorsTrait;
    use DeletedAtAndByAccessorsTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['users:read'])]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\Email]
    #[Groups(['users:read', 'user:write', 'user:put:write', 'user:isUniqueEmail:write'])]
    private ?string $email = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['user:write', 'user:changePassword:write'])]
    #[Assert\Length(min: 6, minMessage: 'Password must be at least {{ limit }} characters long')]
    private ?string $password = null;

    #[ORM\Column(type: 'array')]
    #[Groups(['user:read'])]
    private array $roles = [];

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['user:read'])]
    private ?DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups(['user:read'])]
    private ?DateTimeInterface $updatedAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?DateTimeInterface $deletedAt = null;

    #[ORM\ManyToOne(targetEntity: self::class)]
    #[Groups(['users:read'])]
    private ?self $updatedBy = null;

    #[ORM\ManyToOne(targetEntity: self::class)]
    private ?self $deletedBy = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getRoles(): array
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

    public function eraseCredentials(): void
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function getUserIdentifier(): string
    {
        return (string)$this->getId();
    }

    public function addRole(string $role): self
    {
        if (!in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }

        return $this;
    }

    public function deleteRole(string $role): self
    {
        $roles = $this->roles;

        foreach ($roles as $roleKey => $roleName) {
            if ($roleName === $role) {
                unset($roles[$roleKey]);
                $this->setRoles($roles);
            }
        }

        return $this;
    }

    public function getSalt(): string
    {
        return '';
    }

    public function getUsername(): string
    {
        return $this->getEmail();
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
}
