<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource(
 *   normalizationContext={"groups"={"address:read"}},
 *   denormalizationContext={"groups"={"address:write"}},
 *   attributes={
 *     "security"="is_granted('ROLE_ADMIN') or is_granted('ROLE_CUSTOMER')",
 *     "pagination_items_per_page"=30,
 *   },
 *   collectionOperations={
 *     "get"={
 *       "path"="/address"
 *     },
 *     "post"={"path"="/address"}
 *   },
 *   itemOperations={
 *     "get"={
 *       "path"="/address/{id}",
 *       "security"="is_granted('ROLE_ADMIN') or is_granted('view', object)"
 *     },
 *     "put"={
 *       "path"="/address/{id}",
 *       "security"="is_granted('ROLE_ADMIN') or is_granted('edit', object)"
 *     },
 *     "delete"={
 *       "path"="/address/{id}",
 *       "security"="is_granted('ROLE_ADMIN') or is_granted('delete', object)"
 *       }
 *   }
 * )
 * @ORM\Entity(repositoryClass="App\Repository\PhysicalAddressRepository")
 */
class PhysicalAddress
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="physicalAddresses")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"admin:read", "admin:input"})
     */
    private $owner;

    /**
     * @ORM\Column(type="string", length=15)
     * @Groups({"address:read", "address:write"})
     */
    private $province;

    /**
     * @ORM\Column(type="string", length=30)
     * @Groups({"address:read", "address:write"})
     */
    private $city;

    /**
     * @ORM\Column(type="string", length=150)
     * @Groups({"address:read", "address:write"})
     */
    private $street;

    /**
     * @ORM\Column(type="string", length=150)
     * @Groups({"address:read", "address:write"})
     */
    private $address;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @Groups({"address:read", "address:write"})
     */
    private $lt;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @Groups({"address:read", "address:write"})
     */
    private $lg;

    /**
     * If locked - cannot be edited - has sale orders pointing to it
     * @ORM\Column(type="boolean")
     */
    private $locked = false;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getProvince(): ?string
    {
        return $this->province;
    }

    public function setProvince(string $province): self
    {
        $this->province = $province;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(string $street): self
    {
        $this->street = $street;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getLt(): ?float
    {
        return $this->lt;
    }

    public function setLt(?float $lt): self
    {
        $this->lt = $lt;

        return $this;
    }

    public function getLg(): ?float
    {
        return $this->lg;
    }

    public function setLg(?float $lg): self
    {
        $this->lg = $lg;

        return $this;
    }

    public function getLocked(): ?bool
    {
        return $this->locked;
    }

    public function setLocked(bool $locked): self
    {
        $this->locked = $locked;

        return $this;
    }
}
