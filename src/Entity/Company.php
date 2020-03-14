<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiProperty;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Serializer\Filter\PropertyFilter;


/**
 * @ApiResource(
 *   normalizationContext={"groups"={"company:read"}},
 *   denormalizationContext={"groups"={"company:write"}},
 *   attributes={"security"="is_granted('ROLE_USER')"},
 *   collectionOperations={
 *     "get"= {
 *       "security"="is_granted('ROLE_ADMIN')"
 *     },
 *     "post"={
 *       "security"="is_granted('ROLE_ADMIN') or is_granted('ROLE_CUSTOMER')"
 *     }
 *   },
 *   itemOperations={
 *     "get"={
 *       "security"="is_granted('ROLE_ADMIN') or is_granted('ROLE_TEAMLEADER') or (is_granted('ROLE_CUSTOMER') and object.owner == user)"
 *     },
 *     "put"={
 *       "security"="is_granted('ROLE_ADMIN')"
 *      },
 *      "delete"={
 *       "security"="is_granted('ROLE_ADMIN')"
 *      }
 *   }
 * )
 * @ORM\Entity(repositoryClass="App\Repository\CompanyRepository")
 * @ApiFilter(PropertyFilter::class)
 * @ApiFilter(SearchFilter::class, properties={"name" : "partial"})
 */
class Company
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"company:read", "company:write"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=150)
     * @Groups({"company:read", "company:write"})
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="companies")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\Valid()
     */
    private $owner;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

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
}
