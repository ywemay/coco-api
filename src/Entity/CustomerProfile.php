<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\Security;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Annotation\ApiFilter;

/**
 * @ApiResource(
 *     normalizationContext={"groups"={"customerprofile:read"}},
 *     denormalizationContext={"groups"={"customerprofile:write"}},
 *     attributes={
 *      "security"="is_granted('ROLE_ADMIN')",
 *      "pagination_items_per_page"=30
 *     },
 *     collectionOperations={
 *       "get"={
 *         "security"="is_granted('ROLE_ADMIN')"
 *       },
 *       "post"={
 *         "security"="is_granted('ROLE_ADMIN') or is_granted('ROLE_CUSTOMER')"
 *       }
 *     },
 *     itemOperations={
 *         "get"={
 *          "security"="is_granted('ROLE_ADMIN') or is_granted('view', object)"
 *         },
 *         "put"={
 *          "security"="is_granted('ROLE_ADMIN') or is_granted('edit', object)"
 *         },
 *         "delete"={
 *          "security"="is_granted('ROLE_ADMIN')"
 *         }
 *     }
 * )
 * @ORM\Entity(repositoryClass="App\Repository\CustomerProfileRepository")
 * @UniqueEntity("company")
 * @ORM\HasLifecycleCallbacks
 * @ApiFilter(SearchFilter::class, properties={"company" : "start", "phones" : "start", "emails": "start", "staff": "exact"})
 */
class CustomerProfile
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"customerprofile:read", "saleorder:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     * @Groups({"customerprofile:read", "saleorder:read", "customerprofile:write"})
     * @Assert\NotBlank
     */
    private $company;

    /**
     * @ORM\Column(type="array", nullable=true)
     * @Groups({"customerprofile:read", "customerprofile:write"})
     */
    private $phones = [];

    /**
     * @ORM\Column(type="array", nullable=true)
     * @Groups({"customerprofile:read", "customerprofile:write"})
     */
    private $emails = [];

    /**
     * @ORM\Column(type="string", length=150, nullable=true)
     * @Groups({"customerprofile:read", "customerprofile:write"})
     */
    private $webpage;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\User", mappedBy="customerProfile")
     * @Groups({"customerprofile:read", "customerprofile:write"})
     */
    private $staff;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\PhysicalAddress", mappedBy="customerProfile")
     * @Groups({"customerprofile:read", "customerprofile:write"})
     */
    private $physicalAddresses;

    public function __construct()
    {
        $this->staff = new ArrayCollection();
        $this->saleOrders = new ArrayCollection();
        $this->physicalAddresses = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCompany(): ?string
    {
        return $this->company;
    }

    public function setCompany(string $company): self
    {
        $this->company = $company;

        return $this;
    }

    public function getPhones(): ?array
    {
        return $this->phones;
    }

    public function setPhones(?array $phones): self
    {
        $this->phones = $phones;

        return $this;
    }

    public function getEmails(): ?array
    {
        return $this->emails;
    }

    public function setEmails(?array $emails): self
    {
        $this->emails = $emails;

        return $this;
    }

    public function getWebpage(): ?string
    {
        return $this->webpage;
    }

    public function setWebpage(string $webpage): self
    {
        $this->webpage = $webpage;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getStaff(): Collection
    {
        return $this->staff;
    }

    public function addStaff(User $staff): self
    {
        if (!$this->staff->contains($staff)) {
            $this->staff[] = $staff;
            $staff->setCustomerProfile($this);
        }

        return $this;
    }

    public function removeStaff(User $staff): self
    {
        if ($this->staff->contains($staff)) {
            $this->staff->removeElement($staff);
            // set the owning side to null (unless already changed)
            if ($staff->getCustomerProfile() === $this) {
                $staff->setCustomerProfile(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|PhysicalAddress[]
     */
    public function getPhysicalAddresses(): Collection
    {
        return $this->physicalAddresses;
    }

    public function addPhysicalAddress(PhysicalAddress $physicalAddress): self
    {
        if (!$this->physicalAddresses->contains($physicalAddress)) {
            $this->physicalAddresses[] = $physicalAddress;
            $physicalAddress->setCustomerProfile($this);
        }

        return $this;
    }

    public function removePhysicalAddress(PhysicalAddress $physicalAddress): self
    {
        if ($this->physicalAddresses->contains($physicalAddress)) {
            $this->physicalAddresses->removeElement($physicalAddress);
            // set the owning side to null (unless already changed)
            if ($physicalAddress->getCustomerProfile() === $this) {
                $physicalAddress->setCustomerProfile(null);
            }
        }

        return $this;
    }
}
