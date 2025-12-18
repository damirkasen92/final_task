<?php

namespace App\Entity;

use App\Repository\InventoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\UX\Turbo\Attribute\Broadcast;

#[ORM\Entity(repositoryClass: InventoryRepository::class)]
#[Broadcast]
class Inventory
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
    private ?string $category = null;

    #[ORM\Column]
    private ?bool $isPublic = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $imageUrl = null;

    /**
     * @var Collection<int, Item>
     */
    #[ORM\OneToMany(targetEntity: Item::class, mappedBy: 'inventory', orphanRemoval: true)]
    private Collection $items;

    #[ORM\ManyToOne(inversedBy: 'inventories')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $owner = null;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'writeInventories')]
    private Collection $writers;

    /**
     * @var Collection<int, Tag>
     */
    #[ORM\ManyToMany(targetEntity: Tag::class, mappedBy: 'inventories')]
    private Collection $tags;

    /**
     * @var Collection<int, InventoryField>
     */
    #[ORM\OneToMany(targetEntity: InventoryField::class, mappedBy: 'inventory', orphanRemoval: true)]
    private Collection $inventoryFields;

    public function __construct()
    {
        $this->items = new ArrayCollection();
        $this->writers = new ArrayCollection();
        $this->tags = new ArrayCollection();
        $this->inventoryFields = new ArrayCollection();
    }

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

    public function isPublic(): ?bool
    {
        return $this->isPublic;
    }

    public function setIsPublic(bool $isPublic): static
    {
        $this->isPublic = $isPublic;

        return $this;
    }

    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }

    public function setImageUrl(?string $url): void
    {
        $this->imageUrl = $url;
    }

    /**
     * @return Collection<int, Item>
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    public function addItem(Item $item): static
    {
        if (!$this->items->contains($item)) {
            $this->items->add($item);
            $item->setInventory($this);
        }

        return $this;
    }

    public function removeItem(Item $item): static
    {
        if ($this->items->removeElement($item)) {
            // set the owning side to null (unless already changed)
            if ($item->getInventory() === $this) {
                $item->setInventory(null);
            }
        }

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): static
    {
        $this->owner = $owner;

        return $this;
    }

    public function hasWriteAccess(UserInterface $user): bool
    {
        return $this->writers->contains($user);
    }

    /**
     * @return Collection<int, User>
     */
    public function getWriters(): Collection
    {
        return $this->writers;
    }

    public function addWriter(User $writer): static
    {
        if (!$this->writers->contains($writer)) {
            $this->writers->add($writer);
        }

        return $this;
    }

    public function removeWriter(User $writer): static
    {
        $this->writers->removeElement($writer);

        return $this;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(?string $category): void
    {
        $this->category = $category;
    }

    /**
     * @return Collection<int, Tag>
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tag $tag): static
    {
        if (!$this->tags->contains($tag)) {
            $this->tags->add($tag);
            $tag->addInventory($this);
        }

        return $this;
    }

    public function removeTag(Tag $tag): static
    {
        if ($this->tags->removeElement($tag)) {
            $tag->removeInventory($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, InventoryField>
     */
    public function getInventoryFields(): Collection
    {
        return $this->inventoryFields;
    }

    public function addInventoryField(InventoryField $inventoryField): static
    {
        if (!$this->inventoryFields->contains($inventoryField)) {
            $this->inventoryFields->add($inventoryField);
            $inventoryField->setInventory($this);
        }

        return $this;
    }

    public function removeInventoryField(InventoryField $inventoryField): static
    {
        if ($this->inventoryFields->removeElement($inventoryField)) {
            // set the owning side to null (unless already changed)
            if ($inventoryField->getInventory() === $this) {
                $inventoryField->setInventory(null);
            }
        }

        return $this;
    }
}
