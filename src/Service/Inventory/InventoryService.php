<?php

namespace App\Service\Inventory;

use App\Dto\InventoryDto;
use App\Entity\Inventory;

class InventoryService
{
    public function createInventory(InventoryDto $dto)
    {
        $inventory = new Inventory();
        $inventory->setTitle($dto->title);
        $inventory->setDescription($dto->description);
        $inventory->setCategory($dto->category);
        $inventory->setIsPublic($dto->isPublic);
        $inventory->setImageUrl($dto->imageUrl);

        //TODO logic to find tags and link them to the inventory
        // $inventory->addTag
    }
}
