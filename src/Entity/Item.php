<?php

namespace App\Entity;

use App\Repository\ItemRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ItemRepository::class)]
// #[ORM\Table(
//     uniqueConstraints: [
//         new ORM\UniqueConstraint(
//             name: 'uniq_inventory_custom',
//             columns: ['inventory', 'customId']
//         )
//     ]
// )]
#[ORM\UniqueConstraint(name: 'UNIQ_INVENTORY_CUSTOM_ID', fields: ['inventory', 'customId'])]
class Item
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Inventory::class, inversedBy: 'items')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Inventory $inventory = null;

    #[ORM\Column(length: 255)]
    private ?string $customId = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $createdBy = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $integer1 = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $integer2 = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $integer3 = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $string1 = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $string2 = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $string3 = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $text1 = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $text2 = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $text3 = null;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $bool1 = null;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $bool2 = null;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $bool3 = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $link1 = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $link2 = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $link3 = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getInventory(): ?Inventory
    {
        return $this->inventory;
    }

    public function setInventory(?Inventory $inventory): static
    {
        $this->inventory = $inventory;

        return $this;
    }

    public function getCustomId(): ?string
    {
        return $this->customId;
    }

    public function setCustomId(string $customId): static
    {
        $this->customId = $customId;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?User $createdBy): static
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function getInteger1(): ?int
    {
        return $this->integer1;
    }

    public function setInteger1(int $integer1): static
    {
        $this->integer1 = $integer1;

        return $this;
    }

    public function getInteger2(): ?int
    {
        return $this->integer2;
    }

    public function setInteger2(int $integer2): static
    {
        $this->integer2 = $integer2;

        return $this;
    }

    public function getInteger3(): ?int
    {
        return $this->integer3;
    }

    public function setInteger3(int $integer3): static
    {
        $this->integer3 = $integer3;

        return $this;
    }

    public function getString1(): ?string
    {
        return $this->string1;
    }

    public function setString1(string $string1): static
    {
        $this->string1 = $string1;

        return $this;
    }

    public function getString2(): ?string
    {
        return $this->string2;
    }

    public function setString2(string $string2): static
    {
        $this->string2 = $string2;

        return $this;
    }

    public function getString3(): ?string
    {
        return $this->string3;
    }

    public function setString3(string $string3): static
    {
        $this->string3 = $string3;

        return $this;
    }

    public function getText1(): ?string
    {
        return $this->text1;
    }

    public function setText1(string $text1): static
    {
        $this->text1 = $text1;

        return $this;
    }

    public function getText2(): ?string
    {
        return $this->text2;
    }

    public function setText2(string $text2): static
    {
        $this->text2 = $text2;

        return $this;
    }

    public function getText3(): ?string
    {
        return $this->text3;
    }

    public function setText3(string $text3): static
    {
        $this->text3 = $text3;

        return $this;
    }

    public function getBool1(): ?bool
    {
        return $this->bool1;
    }

    public function setBool1(bool $bool1): static
    {
        $this->bool1 = $bool1;

        return $this;
    }

    public function getBool2(): ?bool
    {
        return $this->bool2;
    }

    public function setBool2(bool $bool2): static
    {
        $this->bool2 = $bool2;

        return $this;
    }

    public function getBool3(): ?bool
    {
        return $this->bool3;
    }

    public function setBool3(bool $bool3): static
    {
        $this->bool3 = $bool3;

        return $this;
    }

    public function getLink1(): ?string
    {
        return $this->link1;
    }

    public function setLink1(string $link1): static
    {
        $this->link1 = $link1;

        return $this;
    }

    public function getLink2(): ?string
    {
        return $this->link2;
    }

    public function setLink2(string $link2): static
    {
        $this->link2 = $link2;

        return $this;
    }

    public function getLink3(): ?string
    {
        return $this->link3;
    }

    public function setLink3(string $link3): static
    {
        $this->link3 = $link3;

        return $this;
    }
}
