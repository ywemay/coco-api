<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource(
 *   normalizationContext={"groups"={"clreport:read"}},
 *   denormalizationContext={"groups"={"clreport:write"}},
 *   attributes={
 *     "security"="is_granted('ROLE_ADMIN') or is_granted('ROLE_CUSTOMER')",
 *     "pagination_items_per_page"=30,
 *   },
 *   collectionOperations={
 *     "get"={
 *       "path"="/clreports"
 *     },
 *     "post"={"path"="/clreports"}
 *   },
 *   itemOperations={
 *     "get"={
 *       "path"="/clreports/{id}",
 *       "security"="is_granted('ROLE_ADMIN') or is_granted('view', object)"
 *     },
 *     "put"={
 *       "path"="/clreports/{id}",
 *       "security"="is_granted('ROLE_ADMIN') or is_granted('edit', object)",
 *       "denormalization_context"={"groups"={"clreport:write", "clreport:create"}}
 *     },
 *     "delete"={
 *       "path"="/clreports/{id}",
 *       "security"="is_granted('ROLE_ADMIN') or is_granted('delete', object)"
 *       }
 *   }
 * )
 * @ORM\EntityListeners("App\Doctrine\ContainerLoadReportListener")
 * @ORM\Entity(repositoryClass="App\Repository\ContainerLoadReportRepository")
 */
class ContainerLoadReport
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"clreport:read"})
     */
    private $createdAt;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"clreport:read", "clreport:write"})
     * @Assert\NotBlank
     */
    private $amountReceived;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"clreport:read", "clreport:write", "clreport:create"})
     * @Assert\NotBlank
     */
    private $amountTip;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User")
     * @Groups({"clreport:read", "clreport:write", "clreport:create"})
     * @Assert\NotBlank
     */
    private $workers;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"clreport:read"})
     */
    private $totalAmount;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"clreport:read"})
     */
    private $companyProfit;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"clreport:read"})
     */
    private $teamleaderTip;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"clreport:read"})
     */
    private $perWorkerAmount;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\SaleOrder", inversedBy="containerLoadReport", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=true)
     * @Groups({"clreport:read", "clreport:write", "clreport:create"})
     */
    private $saleOrder;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"clreport:read"})
     */
    private $balance;

    public function __construct()
    {
        $this->workers = new ArrayCollection();
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

    public function getAmountReceived(): ?int
    {
        return $this->amountReceived;
    }

    public function setAmountReceived(int $amountReceived): self
    {
        $this->amountReceived = $amountReceived;

        return $this;
    }

    public function getAmountTip(): ?int
    {
        return $this->amountTip;
    }

    public function setAmountTip(int $amountTip): self
    {
        $this->amountTip = $amountTip;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getWorkers(): Collection
    {
        return $this->workers;
    }

    public function addWorker(User $worker): self
    {
        if (!$this->workers->contains($worker)) {
            $this->workers[] = $worker;
        }

        return $this;
    }

    public function removeWorker(User $worker): self
    {
        if ($this->workers->contains($worker)) {
            $this->workers->removeElement($worker);
        }

        return $this;
    }

    public function getTotalAmount(): ?int
    {
        return $this->totalAmount;
    }

    public function setTotalAmount(int $totalAmount): self
    {
        $this->totalAmount = $totalAmount;

        return $this;
    }

    public function getCompanyProfit(): ?int
    {
        return $this->companyProfit;
    }

    public function setCompanyProfit(int $companyProfit): self
    {
        $this->companyProfit = $companyProfit;

        return $this;
    }

    public function getTeamleaderTip(): ?int
    {
        return $this->teamleaderTip;
    }

    public function setTeamleaderTip(int $teamleaderTip): self
    {
        $this->teamleaderTip = $teamleaderTip;

        return $this;
    }

    public function getPerWorkerAmount(): ?int
    {
        return $this->perWorkerAmount;
    }

    public function setPerWorkerAmount(int $perWorkerAmount): self
    {
        $this->perWorkerAmount = $perWorkerAmount;

        return $this;
    }

    public function getSaleOrder(): ?SaleOrder
    {
        return $this->saleOrder;
    }

    public function setSaleOrder(SaleOrder $saleOrder): self
    {
        $this->saleOrder = $saleOrder;

        return $this;
    }

    public function getBalance(): ?int
    {
        return $this->balance;
    }

    public function setBalance(int $balance): self
    {
        $this->balance = $balance;

        return $this;
    }
}
