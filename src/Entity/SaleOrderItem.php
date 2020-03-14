<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource(
 *   normalizationContext={"groups"={"saleorderitem:read"}},
 *   denormalizationContext={"groups"={"saleorderitem:write"}},
 *   attributes={
 *     "security"="is_granted('ROLE_ADMIN') or is_granted('ROLE_CUSTOMER')",
 *     "pagination_items_per_page"=30
 *   },
 *   collectionOperations={
 *     "get"={
 *       "path"="/order_items"
 *     },
 *     "post"={"path"="/order_items"}
 *   },
 *   itemOperations={
 *     "get"={
 *       "path"="/order_items/{id}",
 *       "security"="is_granted('ROLE_ADMIN') or is_granted('view', object)"
 *     },
 *     "put"={
 *       "path"="/order_items/{id}",
 *       "security"="is_granted('ROLE_ADMIN') or is_granted('edit', object)"
 *     },
 *     "delete"={
 *       "path"="/order_items/{id}",
 *       "security"="is_granted('ROLE_ADMIN') or is_granted('delete', object)"
 *     }
 *   }
 * )
 * @ORM\Entity(repositoryClass="App\Repository\SaleOrderItemRepository")
 */
class SaleOrderItem
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"saleorder:read", "saleorderitem:read"})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\SaleOrder")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"saleorderitem:read", "saleorderitem:write"})
     */
    private $saleOrder;

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

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSaleOrder(): ?SaleOrder
    {
        return $this->saleOrder;
    }

    public function setSaleOrder(?SaleOrder $saleOrder): self
    {
        $this->saleOrder = $saleOrder;

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

    public function getOwner(): ?User
    {
      return $this->getSaleOrder()->getOwner();
    }

    public function getState(): ?int
    {
      return $this->getSaleOrder()->getState();
    }
}
