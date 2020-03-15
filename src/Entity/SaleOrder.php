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
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     * @Groups({"saleorder:read", "saleorder:write"})
     */
    private $date;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Company")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"saleorder:read", "saleorder:write"})
     * @Assert\Valid()
     */
    private $company;

    /**
     * @ORM\Column(type="smallint")
     * @Assert\Range(min = 0, max = 10)
     * @Groups({"saleorder:read", "saleorder:write"})
     */
    private $state = 0;

    /**
     * @ORM\Column(type="string", length=6)
     * @Groups({"saleorderitem:read", "saleorderitem:write", "saleorder:read", "saleorder:write"})
     */
    private $containerType;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"saleorderitem:read", "saleorderitem:write", "saleorder:read", "saleorder:write"})
     */
    private $startDateTime;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"saleorderitem:read", "saleorderitem:write", "saleorder:read", "saleorder:write"})
     */
    private $price;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"saleorderitem:read", "saleorderitem:write", "saleorder:read", "saleorder:write"})
     */
    private $description;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\ContainerLoadOrder", mappedBy="saleOrder", cascade={"remove"})
     */
    private $containerLoadOrder;


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

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function getOwner(): ?User
    {
      return $this->getCompany()->getOwner();
    }

    public function setCompany(?Company $company): self
    {
        $this->company = $company;

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

    public function setStartDateTime(\DateTimeInterface $startDateTime): self
    {
        $this->startDateTime = $startDateTime;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(?int $price): self
    {
        $this->price = $price;

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

    public function getContainerLoadOrder(): ?ContainerLoadOrder
    {
        return $this->containerLoadOrder;
    }

    public function setContainerLoadOrder(ContainerLoadOrder $containerLoadOrder): self
    {
        $this->containerLoadOrder = $containerLoadOrder;

        // set the owning side of the relation if necessary
        if ($containerLoadOrder->getSaleOrder() !== $this) {
            $containerLoadOrder->setSaleOrder($this);
        }

        return $this;
    }
}
