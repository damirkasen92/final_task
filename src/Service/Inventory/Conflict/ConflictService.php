<?php

namespace App\Service\Inventory\Conflict;

use Doctrine\ORM\EntityManagerInterface;

class ConflictService
{
    public function __construct(
        private EntityManagerInterface $em
    ) {
    }

    public function updateInventory(): void
    {
        $this->em->flush();
    }
}
