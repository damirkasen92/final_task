<?php

namespace App\Service\Inventory;

use App\Dto\ItemFieldDto;
use App\Entity\Inventory;
use App\Entity\ItemField;
use App\Exception\InventoryServiceException;
use App\Repository\InventoryRepository;
use App\Repository\ItemFieldRepository;
use App\Service\Google\GoogleStorageService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\FormInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class InventoryService
{
    private ?Inventory $inventory;

    public function __construct(
        private EntityManagerInterface $em,
        private InventoryRepository $inventoryRepository,
        private GoogleStorageService $googleStorageService,
        private Security $security,
        private ItemFieldRepository $itemFieldRepository,
        private TranslatorInterface $translator
    ) {
    }

    public function getInventoriesWithImage(int $userId)
    {
        $inventories = $this->inventoryRepository->getInventories($userId);

        /** @var Inventory $inventory */
        foreach ($inventories as $inventory) {
            $imageName = $inventory->getImageUrl();

            if ($imageName) {
                $inventory->setImageUrl(
                    $this->googleStorageService->getFileUrl($imageName)
                );
            }
        }

        return $inventories;
    }

    public function createInventory(Inventory $inventory): void
    {
        $this->em->persist($inventory);
        $this->em->flush();
    }

    public function updateInventory()
    {
        $this->em->flush();
    }

    public function deleteInventories(array $inventoryIds): void
    {
        $inventories = $this->inventoryRepository
            ->findBy(['id' => $inventoryIds]);

        foreach ($inventories as $inventory) {
            $this->em->remove($inventory);
        }

        $this->em->flush();
    }

    public function createItemField(ItemField $itemField, Inventory $inventory) {
        $count = $this->itemFieldRepository->count([
            'inventory' => $inventory,
            'type'      => $itemField->getType(),
        ]);

        if ($count >= 3) {
            throw new InventoryServiceException($this->translator->trans('item_field.create_item_field_error', [
                'field' => $itemField->getType()
            ]));
        }

        $itemField->setSlot($itemField->getType()->value . ($count + 1));
        $itemField->setInventory($inventory);
        $itemField->setOrderIndex($inventory->getItemFields()->count() + 1);

        $this->em->persist($itemField);
        $this->em->flush();
    }

    public function setOrder(array $order, Inventory $inventory) {
        $itemFields = $this->itemFieldRepository->findBy([
            'inventory' => $inventory,
        ]);

        foreach ($itemFields as $itemField) {
            $itemField->setOrderIndex(
                $order[$itemField->getId()]
            );
        }

        $this->em->flush();
    }

    public function saveCustomId(Inventory $inventory, array $data) {
        $inventory->setCustomIdFormat($data['elements']);
        $this->em->flush();
    }
}
