<?php

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
 *       "path"="/clorders",
 *       "security"="is_granted('ROLE_ADMIN') or is_granted('ROLE_TEAMLEADER')"
 *     },
 *     "post"={"path"="/clorders"}
 *   },
 *   itemOperations={
 *     "get"={
 *       "path"="/clorders/{id}",
 *       "security"="is_granted('ROLE_ADMIN') or (is_granted('ROLE_TEAMLEADER') and object.assignedTo == user)"
 *     },
 *     "put"={
 *       "path"="/clorders/{id}",
 *     },
 *     "delete"={
 *       "path"="/clorders/{id}",
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

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\SaleOrder", inversedBy="containerLoadOrder")
     */
    private $saleOrder;

    public function __construct()
    {
      $this->setCreatedAt(new \DateTime());
      $this->setAmountDue(0);
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getSaleOrder(): ?SaleOrder
    {
        return $this->saleOrder;
    }

    public function setSaleOrder(?SaleOrder $saleOrder): self
    {
        $this->saleOrder = $saleOrder;

        return $this;
    }
}
