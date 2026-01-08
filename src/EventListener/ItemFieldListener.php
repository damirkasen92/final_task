<?php

namespace App\EventListener;

use App\Entity\ItemField;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Events;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RequestStack;

#[AsEntityListener(event: Events::prePersist, entity: ItemField::class)]
class ItemFieldListener
{
    public function __construct(
        private Security $security,
        private RequestStack $requestStack,
    ) {
    }

    public function prePersist(ItemField $itemField, PrePersistEventArgs $event)
    {

    }
}
