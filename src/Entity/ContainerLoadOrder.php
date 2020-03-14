clorder<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource(
 *   normalizationContext={"groups"={"clorder:read"}},
 *   denormalizationContext={"groups"={"clorder:write"}},
 *   attributes={
 *     "security"="is_granted('ROLE_ADMIN')",
 *     "pagination_items_per_page"=30
 *   },
 *   collectionOperations={
 *     "get"={
 *       "path"="/cl_orders",
 *       "security"="is_granted('ROLE_ADMIN') or is_granted('ROLE_TEAMLEADER')"
 *     },
 *     "post"={"path"="/cl_orders"}
 *   },
 *   itemOperations={
 *     "get"={
 *       "path"="/cl_orders/{id}",
 *       "security"="is_granted('ROLE_ADMIN') or (is_granted('ROLE_TEAMLEADER') and object.assignedTo == user)"
 *     },
 *     "put"={
 *       "path"="/cl_orders/{id}",
 *     },
 *     "delete"={
 *       "path"="/cl_orders/{id}",
 *     }
 *   }
 * )
 * @ORM\Entity(repositoryClass="App\Repository\ContainerLoadOrderRepository")
 */
class ContainerLoadOrder
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"clorder:read"})
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\SaleOrderItem", inversedBy="containerLoadOrder", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"clorder:read", "clorder:write"})
     * @Assert\NotNull
     */
    private $saleOrderItem;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"clorder:read"})
     * @Assert\DateTime
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="containerLoadOrders")
     * @Groups({"clorder:read", "clorder:write"})
     */
    private $assignedTo;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"clorder:read"})
     */
    private $amountDue;

    /**
     * Container Load Order states:
     *   0 - due to execute
     *   1 - work compleated
     *   9 - canceled sale order
     * @ORM\Column(type="smallint")
     * @Groups({"clorder:read", "clorder:write"})
     */
    private $state = 0;

    public function __construct()
    {
      $this->setCreatedAt(date("Y-m-d H:i:s"));
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSaleOrderItem(): ?SaleOrderItem
    {
        return $this->saleOrderItem;
    }

    public function setSaleOrderItem(SaleOrderItem $saleOrderItem): self
    {
        $this->saleOrderItem = $saleOrderItem;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

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

    public function getAmountDue(): ?int
    {
        return $this->amountDue;
    }

    public function setAmountDue(int $amountDue): self
    {
        if (!$this->getId() && !$amountDue) {
          $this->amountDue = $this->getSaleOrderItem()->getSaleOrder()->getPrice();
        }
        $this->amountDue = $amountDue;

        return $this;
    }

    public function getState(): ?int
    {
        return $this->state;
    }

    public function setState(int $state): self
    {
        $this->state = $state;

        return $this;
    }
}
