<?php
namespace App\Service\Inventory;

use App\Entity\Inventory;
use App\Entity\ItemField;
use App\Exception\InventoryServiceException;
use App\Repository\InventoryRepository;
use App\Repository\ItemFieldRepository;
use App\Service\Google\GoogleStorageService;
use Doctrine\ORM\EntityManagerInterface;
use Meilisearch\Client;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

class InventoryService
{
    private const int MAX_SLOT = 3;

    private ?Inventory $inventory;

    public function __construct(
        private EntityManagerInterface $em,
        private InventoryRepository $inventoryRepository,
        private GoogleStorageService $googleStorageService,
        private Security $security,
        private ItemFieldRepository $itemFieldRepository,
        private TranslatorInterface $translator,
        private Client $client
    ) {
    }

    public function getInventoriesWithImage(int $userId, string $query = '')
    {
        $inventories = $this->inventoryRepository->getInventories(
            $userId, $this->getIndexesFromEngine($query)
        );

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

    private function getIndexesFromEngine(string $query): array
    {
        $index   = $this->client->index('inventories');
        $results = $index->search($query, ['limit' => 10])->getHits();
        return ['id' => array_column($results, 'id')];
    }

    public function createInventory(Inventory $inventory): void
    {
        $inventory->setCustomIdFormat([
            [
                'type'  => 'seq',
                'value' => '',
            ],
        ]);

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
        $maxOrderIndex = $this->inventoryRepository->getMaxOrderIndex($inventory);
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
            'type'      => $itemField->getType(),
        ]);

        $usedIndexes = [];

        foreach ($existingSlots as $slot) {
            $suffix = (int) substr(
                $slot->getSlot(),
                \strlen($slot->getType()->value),
            );

            if ($suffix > 0) {
                $usedIndexes[] = $suffix;
            }
        }

        $newIndex = 1;

        while (\in_array($newIndex, $usedIndexes, true)) {
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
                $order[$itemField->getId()]
            );
        }

        $this->em->flush();
    }

    public function saveCustomId(Inventory $inventory, array $data)
    {
        $inventory->setCustomIdFormat($data['elements']);
        $this->em->flush();
    }
}
