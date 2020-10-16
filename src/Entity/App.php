<?php
/** Uncomment file if you use microservices */
//
//namespace App\Entity;
//
//use ApiPlatform\Core\Annotation\ApiResource;
//use App\Repository\AppRepository;
//use Doctrine\Common\Collections\ArrayCollection;
//use Doctrine\Common\Collections\Collection;
//use Doctrine\ORM\Mapping as ORM;
//use Symfony\Component\Serializer\Annotation\Groups;
//
///**
// * @ApiResource(
// *     normalizationContext = {"groups" = {"apps:read"}},
// *     denormalizationContext = {"groups" = {"apps:write"}},
// *     collectionOperations = {
// *          "get" = {
// *          },
// *          "post" = {
// *              "access_control" = "is_granted('ROLE_ADMIN')",
// *          },
// *     },
// *     itemOperations = {
// *          "get" = {
// *              "access_control" = "is_granted('ROLE_ADMIN')"
// *          },
// *          "put" = {
// *              "access_control" = "is_granted('ROLE_ADMIN')",
// *          },
// *     },
// * )
// * @ORM\Entity(repositoryClass=AppRepository::class)
// */
//class App
//{
//    /**
//     * @ORM\Id
//     * @ORM\GeneratedValue
//     * @ORM\Column(type="integer")
//     * @Groups({"apps:read", "users:read"})
//     */
//    private $id;
//
//    /**
//     * @ORM\Column(type="string", length=255)
//     * @Groups({"apps:read", "apps:write", "users:read"})
//     */
//    private $name;
//
//    /**
//     * @ORM\Column(type="string", length=255)
//     * @Groups({"apps:read", "apps:write"})
//     */
//    private $description;
//
//    /**
//     * @ORM\OneToMany(targetEntity=User::class, mappedBy="app")
//     */
//    private $users;
//
//    public function __construct()
//    {
//        $this->users = new ArrayCollection();
//    }
//
//    public function getId(): ?int
//    {
//        return $this->id;
//    }
//
//    public function getName(): ?string
//    {
//        return $this->name;
//    }
//
//    public function setName(string $name): self
//    {
//        $this->name = $name;
//
//        return $this;
//    }
//
//    public function getDescription(): ?string
//    {
//        return $this->description;
//    }
//
//    public function setDescription(string $description): self
//    {
//        $this->description = $description;
//
//        return $this;
//    }
//
//    /**
//     * @return Collection|User[]
//     */
//    public function getUsers(): Collection
//    {
//        return $this->users;
//    }
//
//    public function addUser(User $user): self
//    {
//        if (!$this->users->contains($user)) {
//            $this->users[] = $user;
//            $user->setApp($this);
//        }
//
//        return $this;
//    }
//
//    public function removeUser(User $user): self
//    {
//        if ($this->users->contains($user)) {
//            $this->users->removeElement($user);
//            // set the owning side to null (unless already changed)
//            if ($user->getApp() === $this) {
//                $user->setApp(null);
//            }
//        }
//
//        return $this;
//    }
//}
