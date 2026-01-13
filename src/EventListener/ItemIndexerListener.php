<?php

namespace App\EventListener;

use App\Entity\Item;
use App\Service\Item\ItemIndexer;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Doctrine\ORM\Event\PreRemoveEventArgs;
use Doctrine\ORM\Events;

#[AsEntityListener(event: Events::postPersist, entity: Item::class)]
#[AsEntityListener(event: Events::postUpdate, entity: Item::class)]
#[AsEntityListener(event: Events::preRemove, entity: Item::class)]
class ItemIndexerListener
{
    public function __construct(
        private ItemIndexer $itemIndexer
    ) {
    }

    public function postPersist(Item $item, PostPersistEventArgs $args): void
    {
        $this->itemIndexer->index($item);
    }

    public function postUpdate(Item $item, PostUpdateEventArgs $args): void
    {
        $this->itemIndexer->index($item);
    }

    public function preRemove(Item $item, PreRemoveEventArgs $args): void
    {
        $this->itemIndexer->deleteIndex($item);
    }
}
