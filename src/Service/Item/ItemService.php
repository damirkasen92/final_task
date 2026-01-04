<?php

namespace App\Service\Item;

use App\Dto\ItemDto;
use App\Entity\Inventory;
use App\Entity\Item;
use App\Entity\ItemFieldValue;
use App\Enum\ItemFieldTypes;
use App\Service\Inventory\InventoryService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

class ItemService
{
    public function __construct(
        private InventoryService $inventoryService,
        private EntityManagerInterface $em,
        // private FormFactoryInterface $formFactory,
    ) {
    }

    // public function createItem(ItemDto $dto, FormInterface $form): Item
    // {
    //     $inventory = $this->inventoryService->getInventory($dto->inventoryId);
    //     $item = new Item();
    //     $item->setCreatedAt($dto->createdAt);
    //     $item->setCreatedBy($dto->createdBy);
    //     $item->setInventory($inventory);

    //     $this->setFieldValue($inventory, $item, $form);

    //     $this->em->persist($item);
    //     $this->em->flush();

    //     return $item;
    // }

    // private function setFieldValue(Inventory $inventory, Item $item, FormInterface $form)
    // {
    //     foreach ($inventory->getItemFields() as $field) {
    //         $inputName = 'field_'.$field->getId();
    //         $value = $form->get($inputName)->getData();
    //         $itemValue = new ItemFieldValue();
    //         $itemValue->setItem($item);
    //         $itemValue->setItemField($field);

    //         switch ($field->getType()) {
    //             case ItemFieldTypes::string:
    //             case ItemFieldTypes::text:
    //                 $itemValue->setValueText($value);
    //                 break;
    //             case ItemFieldTypes::integer:
    //                 $itemValue->setValueNumber($value);
    //                 break;
    //             case ItemFieldTypes::bool:
    //                 $itemValue->setValueBoolean($value);
    //                 break;
    //             case ItemFieldTypes::link:
    //                 $itemValue->setValueLink($value);
    //                 break;
    //         }

    //         $this->em->persist($itemValue);
    //     }
    // }
}
