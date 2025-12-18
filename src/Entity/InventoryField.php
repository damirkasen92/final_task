<?php

namespace App\Entity;

use App\Enum\InventoryFieldTypes;
use App\Repository\InventoryFieldRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InventoryFieldRepository::class)]
class InventoryField
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column]
    private ?bool $isDisplayed = null;

    #[ORM\ManyToOne(inversedBy: 'inventoryFields')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Inventory $inventory = null;

    #[ORM\Column(enumType: InventoryFieldTypes::class)]
    private ?InventoryFieldTypes $type = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $orderIndex = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function isDisplayed(): ?bool
    {
        return $this->isDisplayed;
    }

    public function setIsDisplayed(bool $isDisplayed): static
    {
        $this->isDisplayed = $isDisplayed;

        return $this;
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

    public function getType(): ?InventoryFieldTypes
    {
        return $this->type;
    }

    public function setType(InventoryFieldTypes $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getOrderIndex(): ?int
    {
        return $this->orderIndex;
    }

    public function setOrderIndex(int $orderIndex): static
    {
        $this->orderIndex = $orderIndex;

        return $this;
    }
}
