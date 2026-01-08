<?php

namespace App\Service\Inventory\ItemField;

use App\Entity\Inventory;
use App\Entity\ItemField;
use App\Exception\InventoryServiceException;
use App\Repository\ItemFieldRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ItemFieldService
{
    private const int MAX_SLOT = 3;

    public function __construct(
        private TranslatorInterface $translator,
        private ItemFieldRepository $itemFieldRepository,
        private EntityManagerInterface $em
    ) {
    }

    /**
     * @throws InventoryServiceException
     */
    public function createItemField(ItemField $itemField, Inventory $inventory)
    {
        $slotNumber = $this->getFreeSlotNumber($inventory, $itemField);

        if ($slotNumber > self::MAX_SLOT) {
            throw new InventoryServiceException($this->translator->trans('item_field.create_item_field_error', [
                '%field%' => $itemField->getType(),
            ]));
        }

        $itemField->setSlot($itemField->getType()->value . $slotNumber);
        $itemField->setInventory($inventory);
        $maxOrderIndex = $this->itemFieldRepository->getMaxOrderIndex($inventory);
        $itemField->setOrderIndex($maxOrderIndex + 1);

        $this->em->persist($itemField);
        $this->em->flush();
    }

    /**
     * @throws InventoryServiceException
     */
    public function updateItemField(ItemField $itemField, Inventory $inventory)
    {
        $oldType = substr($itemField->getSlot(), 0, \strlen($itemField->getType()->value));

        if ($oldType !== $itemField->getType()->value) {
            $slotNumber = $this->getFreeSlotNumber($inventory, $itemField);

            if ($slotNumber > self::MAX_SLOT) {
                throw new InventoryServiceException($this->translator->trans('item_field.create_item_field_error', [
                    '%field%' => $itemField->getType(),
                ]));
            }

            $itemField->setSlot($itemField->getType()->value . $slotNumber);
        }

        $this->em->flush();
    }

    public function deleteItemFields(array $itemFieldIds): void
    {
        $itemFields = $this->itemFieldRepository->findBy([
            'id' => $itemFieldIds,
        ]);

        foreach ($itemFields as $itemField) {
            $this->em->remove($itemField);
        }

        $this->em->flush();
    }

    private function getFreeSlotNumber(Inventory $inventory, ItemField $itemField)
    {
        $existingSlots = $this->itemFieldRepository->findBy([
            'inventory' => $inventory,
            'type' => $itemField->getType(),
        ]);

        $usedIndexes = [];

        foreach ($existingSlots as $slot) {
            $suffix = (int) substr(
                $slot->getSlot(),
                \strlen($slot->getType()->value),
            );

            if ($suffix > 0) {
                $usedIndexes[$suffix] = true;
            }
        }

        $newIndex = 1;

        while (isset($usedIndexes[$newIndex])) {
            $newIndex++;
        }

        return $newIndex;
    }

    public function setOrder(array $order, Inventory $inventory)
    {
        $itemFields = $this->itemFieldRepository->findBy([
            'inventory' => $inventory,
        ]);

        foreach ($itemFields as $itemField) {
            $itemField->setOrderIndex(
                (int) $order[$itemField->getId()]
            );
        }

        $this->em->flush();
    }
}
