<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Serializer\Filter\PropertyFilter;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\EntityListeners("App\Doctrine\UserListener")
 * @UniqueEntity("username")
 * @ApiResource(
 *   normalizationContext={"groups"={"user:read"}},
 *   denormalizationContext={"groups"={"user:write"}},
 *     attributes={
 *      "security"="is_granted('ROLE_ADMIN')",
 *      "pagination_items_per_page"=30
 *     },
 *     collectionOperations={
 *         "get"={
 *          "security"="is_granted('ROLE_ADMIN') or is_granted('ROLE_TEAMLEADER')"
 *         },
 *         "post"={
 *            "security"="is_granted('ROLE_ADMIN')",
 *            "validation_groups"={"Default", "create"}
 *          },
 *          "regcustomer"={
 *            "path"="/register/customer",
 *            "method"="POST",
 *            "validation_groups"={"regcustomer"},
 *            "denormalization_context"={"groups"={"user:regcustomer"}},
 *            "normalization_context"={"groups"={"user:regcustomer"}}
 *          },
 *          "regteamleader"={
 *            "path"="/register/teamleader",
 *            "method"="POST",
 *            "validation_groups"={"regteamleader"},
 *            "denormalization_context"={"groups"={"user:regteamleader"}},
 *            "normalization_context"={"groups"={"user:regteamleader"}}
 *          },
 *          "regworker"={
 *            "path"="/register/worker",
 *            "method"="POST",
 *            "validation_groups"={"regworker"},
 *            "denormalization_context"={"groups"={"user:regworker"}},
 *            "normalization_context"={"groups"={"user:regworker"}}
 *          }
 *     },
 *     itemOperations={
 *         "get"={
 *          "security"="is_granted('view', object)"
 *         },
 *         "put"={
 *          "security"="is_granted('edit', object)"
 *         },
 *         "delete"={
 *          "security"="is_granted('ROLE_ADMIN')"
 *         }
 *     }
 * )
 * @ApiFilter(PropertyFilter::class)
 * @ApiFilter(SearchFilter::class, properties={"username" : "start", "roles" : "partial", "enabled": "exact"})
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\NotBlank()
     * @Groups({"user:write", "user:read", "user:regcustomer", "user:regteamleader", "user:regworker", "clorder:read"})
     */
    private $username;

    /**
     * @ORM\Column(type="json")
     * @Groups({"user:write", "user:read"})
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @Groups("user:write")
     * @Assert\NotBlank(groups={"create"})
     * @SerializedName("password")
     */
    private $plainPassword;

    /**
     * @ORM\Column(type="string", unique=true, nullable=true)
     */
    private $apiToken;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"user:write", "user:read"})
     */
    private $enabled;

    /**
     * @ORM\Column(type="string", length=120, nullable=true)
     * @Groups({"user:read", "user:write", "user:regcustomer"})
     */
    private $company;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $phone;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $email;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\SaleOrder", mappedBy="owner", orphanRemoval=true, cascade={"persist"})
     */
    private $saleOrders;

    public function __construct()
    {
        $this->saleOrders = new ArrayCollection();
    }

    /**
     * @Groups({"saleorder:read", "user:read"})
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     * @Groups({"saleorder:read", "user:read"})
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    /**
     * @Groups({"user:write"})
     */
    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setPlainRoles(array $roles)
    {
      $allow = ['admin', 'teamleader', 'worker', 'customer'];
      $rez = [];
      foreach ($roles as $value) {
        if (!in_array($allow, $value)) continue;
        $rez[] = 'ROLE_' . mb_strtoupper($value);
      }
      return $this->setRoles($rez);
    }

    /**
     * @Groups({"user:read"})
     */
    public function getPlainRoles(): array
    {
      $roles = $this->getRoles();
      $rez = array();
      foreach ($roles as $k=>$role) {
        if ($role == 'ROLE_USER') continue;
        $rez[$k] = mb_strtolower(preg_replace("/^ROLE\_/", "", $role));
      }
      return $rez;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getPlainPassword(): string
    {
      return (string) $this->plainPassword;
    }

    /**
     * @Groups({"user:write", "user:regcustomer", "user:regteamleader", "user:regworker"})
     */
    public function setPlainPassword(string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    public function getApiToken(): ?string
    {
        return $this->apiToken;
    }

    public function setApiToken(?string $apiToken): self
    {
        $this->apiToken = $apiToken;

        return $this;
    }

    public function getEnabled(): ?bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function __toString(): string
    {
      return $this->getUsername();
    }

    public function getCompany(): ?string
    {
        return $this->company;
    }

    public function setCompany(?string $company): self
    {
        $this->company = $company;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return Collection|SaleOrder[]
     */
    public function getSaleOrders(): Collection
    {
        return $this->saleOrders;
    }

    public function addSaleOrder(SaleOrder $saleOrder): self
    {
        if (!$this->saleOrders->contains($saleOrder)) {
            $this->saleOrders[] = $saleOrder;
            $saleOrder->setOwner($this);
        }

        return $this;
    }

    public function removeSaleOrder(SaleOrder $saleOrder): self
    {
        if ($this->saleOrders->contains($saleOrder)) {
            $this->saleOrders->removeElement($saleOrder);
            // set the owning side to null (unless already changed)
            if ($saleOrder->getOwner() === $this) {
                $saleOrder->setOwner(null);
            }
        }

        return $this;
    }
}
