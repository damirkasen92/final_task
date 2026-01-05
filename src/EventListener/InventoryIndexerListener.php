<?php
namespace App\EventListener;

use App\Entity\Inventory;
use App\Service\Inventory\InventoryIndexer;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Doctrine\ORM\Event\PreRemoveEventArgs;

#[AsEntityListener(event: Events::postPersist, entity: Inventory::class)]
#[AsEntityListener(event: Events::postUpdate, entity: Inventory::class)]
#[AsEntityListener(event: Events::preRemove, entity: Inventory::class)]
class InventoryIndexerListener
{
    public function __construct(
        private InventoryIndexer $inventoryIndexer
    ) {}

    public function postPersist(Inventory $inventory, PostPersistEventArgs $args): void
    {
        $this->inventoryIndexer->index($inventory);
    }

    public function postUpdate(Inventory $inventory, PostUpdateEventArgs $args): void
    {
        $this->inventoryIndexer->index($inventory);
    }

    public function preRemove(Inventory $inventory, PreRemoveEventArgs $args): void
    {
        $this->inventoryIndexer->deleteIndex($inventory);
    }
}
