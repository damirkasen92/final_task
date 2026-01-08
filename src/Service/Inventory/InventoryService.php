<?php

declare(strict_types=1);

namespace App\Service\Inventory;

use App\Dto\Result;
use App\Entity\Inventory;
use App\Entity\ItemField;
use App\Exception\InventoryServiceException;
use App\Repository\InventoryRepository;
use App\Repository\ItemFieldRepository;
use App\Service\FileStorage\FileStorageInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\OptimisticLockException;
use Meilisearch\Client;
use Meilisearch\Exceptions\ApiException;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class InventoryService
{
    private const int MAX_SLOT = 3;
    private const int SEARCH_LIMIT = 10;

    private ?Inventory $inventory;

    public function __construct(
        private EntityManagerInterface $em,
        private InventoryRepository $inventoryRepository,
        private FileStorageInterface $fileStorage,
        private Security $security,
        private ItemFieldRepository $itemFieldRepository,
        private TranslatorInterface $translator,
        private Client $client,
        private SerializerInterface $serializer
    ) {
    }

    public function getInventoriesWithImage(int $userId, string $query = '')
    {
        $inventories = $this->inventoryRepository->getInventories(
            $userId,
            $this->getIndexesFromEngine($query)
        );

        $this->setInventoriesImage($inventories);

        return $inventories;
    }

    private function setInventoriesImage(array $inventories): void
    {
        /** @var Inventory $inventory */
        foreach ($inventories as $inventory) {
            $imageName = $inventory->getImageUrl();

            if ($imageName) {
                $inventory->setImageUrl(
                    $this->fileStorage->getFileUrl($imageName)
                );
            }
        }
    }

    private function getIndexesFromEngine(string $query): array
    {
        if (!$query) {
            return [];
        }

        try {
            $index = $this->client->getIndex('inventories');
        } catch (ApiException $e) {
            if ($e->errorCode === 'index_not_found') {
                $index = $this->client->createIndex('inventories', ['primaryKey' => 'id']);
            }
        }

        $results = $index->search($query, ['limit' => self::SEARCH_LIMIT])->getHits();
        return ['id' => array_column($results, 'id')];
    }

    public function createInventory(Inventory $inventory): void
    {
        $inventory->setCustomIdFormat([
            [
                'type' => 'seq',
                'value' => '',
            ],
        ]);

        $this->em->persist($inventory);
        $this->em->flush();
    }

    public function updateInventory(Inventory $inventoryFromDB, Inventory $inventoryFromForm): Result
    {
        try {
            $this->em->flush();

            return Result::ok();
        } catch (OptimisticLockException $e) {
            return Result::conflict(
                'Conflict',
                $this->serializer->serialize(
                    $inventoryFromDB,
                    'json',
                    ['groups' => ['json']]
                ),
                $this->serializer->serialize(
                    $inventoryFromForm,
                    'json',
                    ['groups' => ['json']]
                ),
            );
        }
    }

    public function handleAutosave(Inventory $inventory, ?UploadedFile $file): array
    {
        $json = [];

        if ($file) {
            $fileStorageName = $this->fileStorage->upload($file);
            $inventory->setImageUrl($fileStorageName);
            $json['imageUrl'] = $this->fileStorage->getFileUrl($fileStorageName);
        }

        $this->em->flush();

        return $json;
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

    public function saveCustomId(Inventory $inventory, array $data)
    {
        $inventory->setCustomIdFormat($data['elements']);
        $this->em->flush();
    }
}
