<?php

namespace App\EventListener;

use App\Entity\Item;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Events;
use Symfony\Bundle\SecurityBundle\Security;

#[AsEntityListener(event: Events::prePersist, entity: Item::class)]
class ItemCreated
{
    public function __construct(
        private Security $security,
    ) {
    }

    public function prePersist(Item $item, PrePersistEventArgs $event)
    {
        $item->setCreatedAt(new \DateTimeImmutable());
        $item->setCreatedBy(
            $this->security->getUser()
        );
    }
}
