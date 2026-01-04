<?php

namespace App\EventListener;

use App\Entity\Inventory;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Events;
use Symfony\Bundle\SecurityBundle\Security;

#[AsEntityListener(event: Events::prePersist, entity: Inventory::class)]
class InventoryCreated
{
    public function __construct(
        private Security $security,
    ) {
    }

    public function prePersist(Inventory $inventory, PrePersistEventArgs $event)
    {
        $inventory->setOwner($this->security->getUser());
    }
}
