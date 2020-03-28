<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\EntityListeners("App\Doctrine\SaleOrderListener")
 * @ApiResource(
 *   normalizationContext={"groups"={"saleorder:read"}},
 *   denormalizationContext={"groups"={"saleorder:write"}},
 *   attributes={
 *     "security"="is_granted('ROLE_ADMIN') or is_granted('ROLE_CUSTOMER')",
 *     "pagination_items_per_page"=30,
 *   },
 *   collectionOperations={
 *     "get"={
 *       "path"="/orders"
 *     },
 *     "post"={"path"="/orders"}
 *   },
 *   itemOperations={
 *     "get"={
 *       "path"="/orders/{id}",
 *       "security"="is_granted('ROLE_ADMIN') or is_granted('view', object)"
 *     },
 *     "put"={
 *       "path"="/orders/{id}",
 *       "security"="is_granted('ROLE_ADMIN') or is_granted('edit', object)"
 *     },
 *     "delete"={
 *       "path"="/orders/{id}",
 *       "security"="is_granted('ROLE_ADMIN') or is_granted('delete', object)"
 *       }
 *   }
 * )
 *
 * @ORM\Entity(repositoryClass="App\Repository\SaleOrderRepository")
 */
class SaleOrder
{
    const FRESH = 0;
    const PLANNED = 1;
    const IN_PROGRESS = 3;
    const DONE = 4;
    const CANCELED = 5;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"saleorder:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     * @Groups({"saleorder:read", "saleorder:write"})
     */
    private $date;

    /**
     * @ORM\Column(type="smallint")
     * @Assert\Range(min = 0, max = 10)
     * @Groups({"saleorder:read"})
     */
    private $state = 0;

    /**
     * @ORM\Column(type="string", length=6)
     * @Groups({"saleorderitem:read", "saleorderitem:write", "saleorder:read", "saleorder:write", "clorder:read"})
     */
    private $containerType;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"saleorderitem:read", "saleorderitem:write", "saleorder:read", "saleorder:write"})
     */
    private $startDateTime;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"saleorder:read", "admin:write"})
     */
    private $price;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"saleorderitem:read", "saleorderitem:write", "saleorder:read", "saleorder:write", "clorder:read"})
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @Groups({"saleorder:read", "admin:write"})
     */
    private $assignedTo;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\ContainerLoadReport", mappedBy="saleOrder", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $containerLoadReport;

    /**
     * @Groups({"saleorder:read", "admin:write"})
     * @ORM\ManyToOne(targetEntity="App\Entity\User", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $owner;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\PhysicalAddress")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"saleorder:read", "saleorder:write"})
     */
    private $address;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\CustomerProfile")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"saleorder:read", "admin:write"})
     */
    private $customer;

    /**
     * @ORM\PrePersist
     */
    public function initDataOnPrePersists()
    {
      if (!$this->getDate()) {
        $this->date = date('Y-m-d');
      }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getState(): ?int
    {
        return $this->state;
    }

    /**
     */
    public function setState(int $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getContainerType(): ?string
    {
        return $this->containerType;
    }

    public function setContainerType(string $containerType): self
    {
        $this->containerType = $containerType;

        return $this;
    }

    public function getStartDateTime(): ?\DateTimeInterface
    {
        return $this->startDateTime;
    }

    /**
     * @Groups({"saleorder:read", "clorder:read"})
     */
    public function getPlainStartDateTime(): ?string
    {
      return $this->getStartDateTime()->format('Y-m-d H:i');
    }

    public function setStartDateTime(\DateTimeInterface $startDateTime): self
    {
        $this->startDateTime = $startDateTime;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price / 100;
    }

    public function setPrice(?float $price): self
    {
        $this->price = intval($price * 100);

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getAssignedTo(): ?User
    {
        return $this->assignedTo;
    }

    public function setAssignedTo(?User $assignedTo): self
    {
        $this->assignedTo = $assignedTo;
        return $this;
    }

    public function getContainerLoadReport(): ?ContainerLoadReport
    {
        return $this->containerLoadReport;
    }

    public function setContainerLoadReport(ContainerLoadReport $containerLoadReport): self
    {
        $this->containerLoadReport = $containerLoadReport;

        // set the owning side of the relation if necessary
        if ($containerLoadReport->getSaleOrder() !== $this) {
            $containerLoadReport->setSaleOrder($this);
        }

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    public function getAddress(): ?PhysicalAddress
    {
        return $this->address;
    }

    public function setAddress(?PhysicalAddress $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getCustomer(): ?CustomerProfile
    {
        return $this->customer;
    }

    public function setCustomer(?CustomerProfile $customer): self
    {
        $this->customer = $customer;

        return $this;
    }
}
