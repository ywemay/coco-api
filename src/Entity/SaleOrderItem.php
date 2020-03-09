<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass="App\Repository\SaleOrderItemRepository")
 */
class SaleOrderItem
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\SaleOrder")
     * @ORM\JoinColumn(nullable=false)
     */
    private $saleOrder;

    /**
     * @ORM\Column(type="string", length=6)
     */
    private $containerType;

    /**
     * @ORM\Column(type="datetime")
     */
    private $startDateTime;

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
}
