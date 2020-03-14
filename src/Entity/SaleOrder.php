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
 *     "pagination_items_per_page"=30
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
     * @ORM\OneToMany(targetEntity="App\Entity\SaleOrderItem", mappedBy="saleOrder", cascade={"persist"}, orphanRemoval=true)
     * @Groups({"saleorder:read", "saleorder:write"})
     * @Assert\Valid()
     */
    private $saleOrderItems;

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

    /**
     * @return Collection|SaleOrderItem[]
     */
    public function getSaleOrderItems(): Collection
    {
        return $this->saleOrderItems;
    }

    public function addSaleOrderItem(SaleOrderItem $saleOrderItem): self
    {
        if (!$this->saleOrderItems->contains($saleOrderItem)) {
            $this->saleOrderItems[] = $saleOrderItem;
            $saleOrderItem->setSaleOrder($this);
        }
        return $this;
    }

    public function removeSaleOrderItem(SaleOrderItem $saleOrderItem): self
    {
        if ($this->saleOrderItems->contains($saleOrderItem)) {
            $this->saleOrderItems->removeElement($saleOrderItem);
            // set the owning side to null (unless already changed)
            if ($saleOrderItem->getSaleOrder() === $this) {
                $saleOrderItem->setSaleOrder(null);
            }
        }
        return $this;
    }

}
